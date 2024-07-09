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
        Schema::create('consigned_products', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('product_variation_id');
            $table->unsignedBigInteger('user_id');
            $table->string('chasis_no')->unique();
            $table->boolean('in_stock');

            $table->timestamps();

            $table->foreign('product_variation_id')->references('id')->on('product_variations')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consigned_products');
    }
};
