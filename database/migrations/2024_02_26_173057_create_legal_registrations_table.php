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
        Schema::create('legal_registrations', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('order_id')->nullable();

            $table->text('params')->nullable(); // All the params separated by comma

            $table->string('id_card_front')->nullable();
            $table->string('id_card_back')->nullable();

            $table->string('signature_circular')->nullable();
            $table->string('operating_certificate')->nullable();
            $table->string('registry_gazzete')->nullable();
            $table->string('circular_indentity_front')->nullable();
            $table->string('circular_indentity_back')->nullable();
            $table->string('power_of_attorney')->nullable();


            $table->enum('approved_by_erp', ['pending', 'declined', 'approved'])->default('pending');

            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legal_registrations');
    }
};
