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
        Schema::create('menu_section_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_section_id');
            $table->foreignId('lang_id');
            $table->string('title');
            $table->timestamps();

            $table->foreign('menu_section_id')->references('id')->on('menu_sections')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_section_translations');
    }
};
