<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\User;
use Illuminate\Console\Command;

class AddBrandNamesToProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-brand-names';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add brand names to products';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $products = Product::get();

        foreach ($products as $product) {
            $brand = explode(" ", $product->currentTranslation->product_name);

            $product->update(['brand_name' => $brand[0]]);
        }

        $this->info('Updated brand names');
    }
}
