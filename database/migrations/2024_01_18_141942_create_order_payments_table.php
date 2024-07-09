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
        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->decimal('payment_amount', 15, 2);
            $table->enum('payment_type', ['H', 'K', 'S', 'P']);
            $table->unsignedBigInteger('bank_account_id')->nullable();
            $table->enum('approved_by_erp', ['Y', 'N'])->default('N');
            $table->unsignedInteger('number_of_installments')->nullable();
            $table->decimal('collected_payment', 15, 2);
            $table->string('credit_card_no', 3)->nullable();
            $table->string('description')->nullable();
            $table->string('payment_ref_no');
            $table->unsignedBigInteger('user_id');
            $table->text('payment_gateway_response')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('bank_account_id')->references('id')->on('bank_accounts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_payments');
    }
};
