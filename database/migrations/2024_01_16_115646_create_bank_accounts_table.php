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
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_name');
            $table->unsignedBigInteger('country_id')->default(1);
            $table->unsignedBigInteger('currency_id')->default(1);
            $table->unsignedBigInteger('bank_id');
            $table->string('branch_name')->nullable();
            $table->string('branch_code')->nullable();
            $table->string('account_no');
            $table->string('iban');
            $table->string('swift_code')->nullable();
            $table->timestamps();

            $table->foreign('bank_id')->references('id')->on('banks');
            $table->foreign('country_id')->references('id')->on('countries');
            $table->foreign('currency_id')->references('id')->on('currencies');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};
