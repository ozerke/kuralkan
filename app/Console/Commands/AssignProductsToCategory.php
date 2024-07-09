<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Console\Command;

class AssignProductsToCategory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'category:assign-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assigns all products to chosen category';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $categoryId = $this->ask('What category ID should be assigned?');

        $category = Category::findOrFail((int)$categoryId);

        $products = Product::all();

        foreach ($products as $product) {
            $product->categories()->firstOrCreate(['category_id' => $category->id, 'display_order' => 0]);
        }
    }
}
