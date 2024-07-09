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
            $table->string('stage', 50)->default('application_fee')->index();
            $table->unsignedSmallInteger('findeks_request_status')->nullable();
            $table->unsignedSmallInteger('findeks_request_result')->nullable();
            $table->unsignedSmallInteger('findeks_merged_order')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_agreements', function (Blueprint $table) {
            $table->dropColumn('stage');
            $table->dropColumn('findeks_request_status');
            $table->dropColumn('findeks_request_result');
            $table->dropColumn('findeks_merged_order');
        });
    }
};
