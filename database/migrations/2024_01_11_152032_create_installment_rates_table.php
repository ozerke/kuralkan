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
        Schema::create('installment_rates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cc_group_id')->nullable();
            $table->unsignedBigInteger('bank_id');
            $table->unsignedInteger('number_of_months');
            $table->float('rate');
            $table->timestamps();

            $table->foreign('bank_id')->references('id')->on('banks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installment_rates');
    }
};
