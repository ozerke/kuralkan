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
            $table->text('notary_document_back')->nullable();
            $table->boolean('notary_document_back_rejected')->default(false);

            $table->text('front_side_id')->nullable();
            $table->boolean('front_side_id_rejected')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_agreements', function (Blueprint $table) {
            $table->dropColumn('notary_document_back');
            $table->dropColumn('notary_document_back_rejected');
            $table->dropColumn('front_side_id');
            $table->dropColumn('front_side_id_rejected');
        });
    }
};
