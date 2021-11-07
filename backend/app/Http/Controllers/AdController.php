<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ad;
use App\Models\ImageChecker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class AdController extends Controller
{
    public function listAds(Request $request)
    {
        $ads = Ad::orderBy('score', 'desc')->get();

        return response()->json($ads);
    }

    public function getAdUniqueCode(Request $request)
    {
        $uuid = Str::uuid();

        $newImageCheck         = new ImageChecker();
        $newImageCheck["uuid"] = $uuid;
        $newImageCheck["path"] = "";
        $newImageCheck->save();

        return response()->json([
            'id' => $uuid
        ]);
    }

    public function preProcessImage(Request $request)
    {
        $id    = $request->id;
        $ext   = $request->file('file')->extension();
        $path  = $request->file('file')->storeAs('images', "{$id}.{$ext}");
        $photo = Storage::get($path);

        $imageCheck = ImageChecker::where('uuid', $request->id)->first();

        if (!isset($imageCheck)) {
            $imageCheck         = new ImageChecker();
            $imageCheck["uuid"] = $request->id;
        }

        $imageCheck["path"] = "/" . $path;
        $imageCheck->save();

        $url = env('ML_URL') . ":5000";
        $url = "{$url}/process-image";

        $response = Http::attach('file', $photo, "{$id}.{$ext}")->post($url, [ 'id' => $id ]);

        return response()->json($response->body());
    }

    public function setImageItems(Request $request)
    {
        $item = ImageChecker::where('uuid', $request->id)->first();
        $item['items'] = $request->items;
        $item->save();

        return response()->json(true);
    }

    public function getImageItems(Request $request)
    {
        $items      = ImageChecker::where('uuid', $request->id)->first();
        $done       = $items['items'] != null;
        $imageItems = json_decode($items['items'] ?? '[]', true);

        foreach ($imageItems as $index => $item) {
            $imageItems[$index]['translations'] = implode(', ', $item['translations']);
        }

        return response()->json([
            'done'  => $done,
            'items' => $imageItems,
            'ii'    => $items['imageImage'] ?? -1,
            'it'    => $items['imageTitle'] ?? -1,
            'id'    => $items['imageDesc'] ?? -1,
            'td'    => $items['titleDesc'] ?? -1,
            'ov'    => $items['overall'] ?? -1,
        ]);
    }

    public function checkImageImage(Request $request)
    {
        $url  = env('ML_URL') . ":5000";
        $url  = "{$url}/process-image-image";

        $items = ImageChecker::where('uuid', $request->id)->first();

        $data = [
            'id'    => $request->id,
            'image' => $items['items'] ?? '[]',
        ];
        $response = Http::asForm()->post($url, $data);

        return response()->json(true);
    }

    public function checkImageTitle(Request $request)
    {
        $url  = env('ML_URL') . ":5000";
        $url  = "{$url}/process-image-title";

        $items = ImageChecker::where('uuid', $request->id)->first();

        $data = [
            'id'    => $request->id,
            'title' => $request->title,
            'image' => $items['items'] ?? '[]',
        ];
        $response = Http::asForm()->post($url, $data);

        return response()->json(true);
    }

    public function checkImageDescription(Request $request)
    {
        $url  = env('ML_URL') . ":5000";
        $url  = "{$url}/process-image-description";

        $items = ImageChecker::where('uuid', $request->id)->first();

        $data = [
            'id'          => $request->id,
            'description' => $request->description,
            'image'       => $items['items'] ?? '[]',
        ];
        $response = Http::asForm()->post($url, $data);

        return response()->json(true);
    }

    public function checkTitleDescription(Request $request)
    {
        $url  = env('ML_URL') . ":5000";
        $url  = "{$url}/process-title-description";
        $data = [
            'id'          => $request->id,
            'title'       => $request->title,
            'description' => $request->description,
        ];
        $response = Http::asForm()->post($url, $data);

        return response()->json($data);
    }

    public function calculateOverall(Request $request)
    {
        $url  = env('ML_URL') . ":5000";
        $url  = "{$url}/process-overall";

        $items = ImageChecker::where('uuid', $request->id)->first();

        $data = [
            'id'     => $request->id,
            'image'  => $items['imageImage'] ?? 0,
            'imgTit' => $items['imageTitle'] ?? 0,
            'imgDes' => $items['imageDesc'] ?? 0,
            'titDes' => $items['titleDesc'] ?? 0,
        ];
        $response = Http::asForm()->post($url, $data);

        return response()->json($data);
    }

    public function createNewAd(Request $request)
    {
        $imageCheck = ImageChecker::where('uuid', $request->id)->first();

        $value = str_replace("R$ ", "", $request['value']);
        $value = str_replace(".", "", $value);
        $value = str_replace(",", ".", $value);

        $ad = new Ad();
        $ad['title'] = $request['title'];
        $ad['description'] = $request['description'];
        $ad['image'] = $imageCheck->path ??  '/img/semimagem.png';
        $ad['value'] = floatval($value);
        $ad['category'] = "";
        $ad['type'] = "";
        $ad['score'] = $imageCheck->overall ?? 0;

        $ad->save();

        return response()->json([
            'status' => true
        ]);
    }

    public function getProcessedFeedback(Request $request)
    {
        $scores = ImageChecker::where('uuid', $request->id)->first();

        switch ($request->process) {
            case "ii":
                $scores['imageImage'] = floatval($request->score);
                break;
            case "it":
                $scores['imageTitle'] = floatval($request->score);
                break;
            case "id":
                $scores['imageDesc'] = floatval($request->score);
                break;
            case "td":
                $scores['titleDesc'] = floatval($request->score);
                break;
            case "ov":
                $scores['overall'] = floatval($request->score);
                break;
        }

        $scores->save();

        return response()->json([
            'status' => true
        ]);
    }

    public function resetAllAds()
    {
        Artisan::call('migrate:refresh');

        return response()->json([
            'status' => true
        ]);
    }
}
