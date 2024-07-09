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
        Schema::create('installment_options', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('down_payment_id')->nullable();
            $table->string('installments');
            $table->string('monthly_payment');

            $table->timestamps();

            $table->foreign('down_payment_id')->references('id')->on('down_payments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installment_options');
    }
};
