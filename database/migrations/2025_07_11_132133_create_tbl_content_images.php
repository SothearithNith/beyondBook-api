<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_content_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('content_id');
            $table->foreign('content_id')->references('id')->on('tbl_content')->onDelete('cascade');

            $table->string('image_path'); // e.g., 'uploads/content/image1.jpg'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_content_images');
    }
};
