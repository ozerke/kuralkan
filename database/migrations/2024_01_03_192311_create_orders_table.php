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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no', 25);
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('invoice_user_id');
            $table->unsignedBigInteger('delivery_user_id');
            $table->unsignedBigInteger('product_variation_id');
            $table->enum('payment_type', ['H', 'K', 'S', 'P'])->nullable();
            $table->string('product_name', 255);
            $table->decimal('total_amount', 15, 2);
            $table->string('chasis_no', 100)->nullable();
            $table->string('motor_no', 100)->nullable();
            $table->string('invoice_link', 255)->nullable();
            $table->string('temprorary_licence_doc_link', 255)->nullable();
            $table->string('plate_printing_docL_link', 255)->nullable();
            $table->unsignedBigInteger('bank_id')->nullable();
            $table->string('erp_order_id', 100)->nullable();
            $table->enum('from_stock', ['Y', 'N'])->default('N');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('invoice_user_id')->references('id')->on('users');
            $table->foreign('delivery_user_id')->references('id')->on('users');
            $table->foreign('product_variation_id')->references('id')->on('product_variations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
