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
        Schema::create('product_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id');
            $table->foreignId('lang_id')->default(1);
            $table->string('product_name', 255);
            $table->text('description');
            $table->text('short_description');
            $table->string('seo_title', 255);
            $table->text('seo_desc');
            $table->text('seo_keywords');
            $table->text('delivery_info');
            $table->text('faq');
            $table->text('documents');
            $table->string('slug', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_translations');
    }
};
