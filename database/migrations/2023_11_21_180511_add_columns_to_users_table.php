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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->string('erp_user_name')->nullable();
            $table->string('erp_user_id', 50)->nullable();
            $table->string('site_user_name');
            $table->string('site_user_surname');
            $table->text('address')->nullable();
            $table->foreignId('district_id');
            $table->string('phone');
            $table->string('erp_email')->nullable();
            $table->string('user_no', 30)->nullable();
            $table->enum('user_active', ['Y', 'N'])->default('Y');
            $table->string('postal_code', 10)->nullable();
            $table->enum('company', ['Y', 'N'])->default('N');
            $table->string('company_name', 150)->nullable();
            $table->string('national_id', 11)->nullable();
            $table->string('tax_id', 11)->nullable();
            $table->string('tax_office', 50)->nullable();
            $table->date('date_of_birth');
            $table->tinyInteger('shop')->default(0);
            $table->tinyInteger('service')->default(0);
            $table->float('latitude')->default(0);
            $table->float('longtitude')->default(0);
            $table->integer('root_user')->default(0);
            $table->json('sub_user_permission')->nullable();
            $table->integer('registered_by')->nullable();
            $table->integer('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
