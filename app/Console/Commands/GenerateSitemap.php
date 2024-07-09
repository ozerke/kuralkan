<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Console\Command;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-sitemap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the sitemap for eKuralkan website';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sitemapPath = public_path('sitemap.xml');

        $sitemap = SitemapGenerator::create(config('app.url'))->getSitemap();

        $products = Product::displayable()->get();

        foreach ($products as $product) {
            $slug = $product->currentTranslation->slug;

            if (!$slug) continue;

            $urlItem = Url::create("/{$slug}")
                ->setLastModificationDate($product->updated_at)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                ->setPriority(0.9);


            $variationSaleData = $product->getVariationsSaleData();

            if (count($variationSaleData) > 0) {
                foreach ($variationSaleData as $variationData) {
                    if (count($variationData['urls']) > 0) {
                        foreach ($variationData['urls'] as $url) {
                            $urlItem->addImage($url, $product->currentTranslation->product_name);
                        }
                    }
                }
            }

            $sitemap->add($urlItem);
        }

        $categories = Category::all();

        foreach ($categories as $category) {
            $slug = $category->currentTranslation->slug;

            if (!$slug) continue;

            $urlItem = Url::create("/{$slug}")
                ->setLastModificationDate($category->updated_at)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                ->setPriority(0.9);

            if ($category->firstProduct->firstDisplayableVariation->firstMedia ?? false) {
                $photoUrl = $category->firstProduct->firstDisplayableVariation->firstMedia->photo_url;

                $urlItem->addImage($photoUrl, $category->currentTranslation->category_name);
            }

            $sitemap->add($urlItem);
        }

        $sitemap->writeToFile($sitemapPath);

        $productCount = count($products);
        $categoryCount = count($categories);

        $this->info("Sitemap generated successfully.");
        $this->info("Products count: {$productCount}");
        $this->info("Categories count: {$categoryCount}");
    }
}
