<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tbl_sub_category', function (Blueprint $table) {
            $table->id();
            $table->string('sub_category_kh');
            $table->string('sub_category_en');
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('tbl_category')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_sub_category');
    }
};
