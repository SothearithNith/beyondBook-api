<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_content', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('tbl_category')->onDelete('cascade');

            $table->unsignedBigInteger('sub_category_id');
            $table->foreign('sub_category_id')->references('id')->on('tbl_sub_category')->onDelete('cascade');

            $table->string('title_kh');
            $table->string('title_en');
            $table->string('description_kh');
            $table->string('description_en');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_content');
    }
};
