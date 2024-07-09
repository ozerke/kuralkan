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
        Schema::create('ebonds', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('sales_agreement_id');
            $table->unsignedBigInteger('order_id');
            $table->string('erp_order_id')->nullable()->index();

            $table->string('e_bond_no')->unique();
            $table->date('due_date')->index();
            $table->decimal('bond_amount', 15, 2);
            $table->decimal('remaining_amount', 15, 2);
            $table->string('bond_description')->nullable();
            $table->enum('penalty', ['T', 'F'])->default('F')->index();
            $table->boolean('penalty_sms_sent')->default(false);
            $table->boolean('penalty_email_sent')->default(false);
            $table->unsignedBigInteger('user_id');

            $table->timestamps();

            $table->foreign('sales_agreement_id')->references('id')->on('sales_agreements')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ebonds');
    }
};
