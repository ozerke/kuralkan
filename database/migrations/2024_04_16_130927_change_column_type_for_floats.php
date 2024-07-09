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
        Schema::table('down_payments', function (Blueprint $table) {
            $table->float('amount')->change();
        });

        Schema::table('installment_options', function (Blueprint $table) {
            $table->unsignedInteger('installments')->change();
            $table->float('monthly_payment')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('down_payments', function (Blueprint $table) {
            $table->string('amount')->change();
        });

        Schema::table('installment_options', function (Blueprint $table) {
            $table->string('installments')->change();
            $table->string('monthly_payment')->change();
        });
    }
};
