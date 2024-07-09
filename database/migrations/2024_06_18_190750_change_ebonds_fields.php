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
        Schema::table('ebonds', function (Blueprint $table) {
            $table->boolean('penalty')->default(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ebonds', function (Blueprint $table) {
            $table->enum('penalty', ['T', 'F'])->default('F')->change();
        });
    }
};
