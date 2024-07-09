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
        Schema::table('sales_agreements', function (Blueprint $table) {
            $table->text('notary_document')->nullable();
            $table->boolean('notary_document_rejected')->default(false);
            $table->text('notary_document_rejection_reason')->nullable();
        });

        Schema::table('order_payments', function (Blueprint $table) {
            $table->string('bond_no')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_agreements', function (Blueprint $table) {
            $table->dropColumn('notary_document');
            $table->dropColumn('notary_document_rejected');
            $table->dropColumn('notary_document_rejection_reason');
        });

        Schema::table('order_payments', function (Blueprint $table) {
            $table->dropColumn('bond_no');
        });
    }
};
