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
        Schema::create('sales_agreements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id');
            $table->decimal('down_payment_amount', 15, 2);
            $table->unsignedTinyInteger('number_of_installments');
            $table->decimal('agreement_total_amount', 15, 2);
            $table->string('findeks_request_id')->nullable();
            $table->text('agreement_document_link')->nullable();
            $table->foreignId('application_fee_payment_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_agreements');
    }
};
