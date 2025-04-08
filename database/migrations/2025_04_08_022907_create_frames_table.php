<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('frames', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversion_id')->constrained()->onDelete('cascade');
            $table->string('filename');
            $table->integer('frame_number');
            $table->string('file_path');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('frames');
    }
};