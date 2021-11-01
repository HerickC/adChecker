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
        $ads = Ad::all();

        return response()->json($ads);
    }

    public function getAdUniqueCode(Request $request)
    {
        return response()->json([
            'id' => Str::uuid()
        ]);
    }

    public function preProcessImage(Request $request)
    {
        $newImageCheck = new ImageChecker();
        $newImageCheck["uuid"] = $request->id;
        $newImageCheck->save();
        $id    = $request->id;
        $ext   = $request->file('file')->extension();
        $path  = $request->file('file')->storeAs('images', "{$id}.{$ext}");
        $photo = Storage::get($path);

        $url = env('ML_URL') . ":5000";
        $url = "{$url}/process-image";

        $response = Http::attach('file', $photo, "{$id}.{$ext}")
            ->post($url, [
                'id' => $id
            ]);

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
        $items = ImageChecker::where('uuid', $request->id)->first();
        $items = json_decode($items['items'] ?? '[]');

        return response()->json([
            'items' => $items
        ]);
    }

    public function resetAllAds()
    {
        Artisan::call('migrate:refresh --seed');

        return response()->json([
            'status' => true
        ]);
    }
}
