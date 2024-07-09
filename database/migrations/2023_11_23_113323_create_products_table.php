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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('stock_code', 20);
            $table->string('gtip_code')->nullable();
            $table->foreignId('currency_id')->default(1);
            $table->enum('display', ['t', 'f'])->default('f');
            $table->foreignId('country_id')->default(1);
            $table->unsignedTinyInteger('display_order');
            $table->enum('seo_no_index', ['', 'noindex'])->default('');
            $table->enum('seo_no_follow', ['', 'nofollow'])->default('');
            $table->enum('new_product', ['Y', 'N'])->default('N');
            $table->enum('featured_product', ['Y', 'N'])->default('N');
            $table->foreignId('bread_crumb_category_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
