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
        Schema::create('product_technical_specifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id');
            $table->unsignedTinyInteger('display_order');
            $table->foreignId('lang_id');
            $table->string('specification');
            $table->string('value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_technical_specifications');
    }
};
