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
        Schema::create('product_variations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id');
            $table->foreignId('color_id');
            $table->enum('display', ['t', 'f'])->default('f');
            $table->unsignedTinyInteger('display_order');
            $table->string('barcode', 20)->nullable();
            $table->unsignedSmallInteger('total_stock');
            $table->date('estimated_delivery_date');
            $table->double('price', 15, 2)->default(0);
            $table->unsignedTinyInteger('vat_ratio')->default(0);
            $table->double('discount', 15, 2)->default(0);
            $table->enum('discount_type', ['', 'percentage', 'value'])->default('');
            $table->string('variant_key', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variations');
    }
};
