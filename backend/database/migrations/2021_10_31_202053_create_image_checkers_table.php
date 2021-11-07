<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImageCheckersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('image_checkers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('path');
            $table->json('items')->nullable();
            $table->float('imageImage')->nullable();
            $table->float('imageTitle')->nullable();
            $table->float('imageDesc')->nullable();
            $table->float('titleDesc')->nullable();
            $table->float('overall')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('image_checkers');
    }
}
