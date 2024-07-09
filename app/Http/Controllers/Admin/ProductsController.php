<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\CacheServiceInterface;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Language;
use App\Models\Product;
use App\Models\ProductMedia;
use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use App\Utils\StorageUtils;
use Exception;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    protected $cacheService;

    public function __construct(CacheServiceInterface $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function index(Request $request)
    {
        $products = Product::all();

        return view('admin.products.index')->with([
            'products' => $products,
        ]);
    }

    public function toggleNewProduct(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);

            $product->update([
                'new_product' => $product->new_product === 'N' ? 'Y' : 'N'
            ]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Toggle new product', ['e' => $e]);
            return back()->with('error', 'Could not update the product');
        }
    }

    public function toggleDisplay(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);

            $product->update([
                'display' => $product->display === 'f' ? 't' : 'f'
            ]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Toggle display product', ['e' => $e]);
            return back()->with('error', 'Could not update the product');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $product = Product::findOrFail($id);
            $categories = Category::all();

            return view('admin.products.details')->with([
                'product' => $product,
                'categories' => $categories
            ]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Edit product', ['e' => $e]);

            return back()->with('error', 'An error occured');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $product = Product::findOrFail($id);

            $this->cacheService->forget("currentTranslation.{$id}.en");
            $this->cacheService->forget("currentTranslation.{$id}.tr");

            $en = $product->getTranslation('en');
            $tr = $product->getTranslation('tr');

            $product->update([
                'new_product' => $request->has('new_product') ? 'Y' : 'N',
                'display' => $request->has('display') ? 't' : 'f',
                'seo_no_index' => $request->has('seo-no-index') ? 'noindex' : '',
                'seo_no_follow' => $request->has('seo-no-follow') ? 'nofollow' : '',
                'display_order' => $request->input('display-order'),
                'bread_crumb_category_id' => $request->input('breadcrumb-category'),
            ]);

            $tr->update([
                'product_name' => $request->input('tr-product-name') ?? '',
                'description' => $request->input('tr-product-description') ?? '',
                'short_description' => $request->input('tr-product-short-description') ?? null,
                'seo_title' => $request->input('tr-seo-title') ?? '',
                'seo_desc' => $request->input('tr-seo-description') ?? '',
                'seo_keywords' => $request->input('tr-seo-keywords') ?? '',
                'delivery_info' => $request->input('tr-product-delivery-info') ?? '',
                'faq' => $request->input('tr-product-faq') ?? '',
                'slug' => $request->input('tr-slug'),
                'documents' => $request->input('tr-product-documents') ?? '',
            ]);

            if ($en) {
                $en->update([
                    'product_name' => $request->input('en-product-name'),
                    'description' => $request->input('en-product-description'),
                    'short_description' => $request->input('en-product-short-description') ?? null,
                    'seo_title' => $request->input('en-seo-title'),
                    'seo_desc' => $request->input('en-seo-description'),
                    'seo_keywords' => $request->input('en-seo-keywords'),
                    'delivery_info' => $request->input('en-product-delivery-info'),
                    'faq' => $request->input('en-product-faq'),
                    'slug' => $request->input('en-slug'),
                    'documents' => $request->input('en-product-documents') ?? '',
                ]);
            } else {
                $product->translations()->create([
                    'lang_id' => Language::AVAILABLE['en'],
                    'product_name' => $request->input('en-product-name'),
                    'description' => $request->input('en-product-description'),
                    'short_description' => $request->input('en-product-short-description') ?? null,
                    'seo_title' => $request->input('en-seo-title'),
                    'seo_desc' => $request->input('en-seo-description'),
                    'seo_keywords' => $request->input('en-seo-keywords'),
                    'delivery_info' => $request->input('en-product-delivery-info'),
                    'faq' => $request->input('en-product-faq'),
                    'slug' => $request->input('en-slug'),
                    'documents' => $request->input('en-product-documents') ?? '',
                ]);
            }

            $categories = $request->input('categories');

            $product->categories()->delete();

            if ($categories) {
                foreach ($categories as $index => $catId) {
                    $product->categories()->create(['category_id' => $catId, 'display_order' => $index]);
                }
            }

            return back()->with('success', 'Updated successfully');
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Update product', ['e' => $e]);

            return back()->with('error', 'An error occured');
        }
    }

    public function editImages(string $id)
    {
        try {
            $product = Product::findOrFail($id);

            return view('admin.products.image-gallery')->with([
                'product' => $product,
            ]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Edit images product', ['e' => $e]);

            return back()->with('error', 'An error occured');
        }
    }

    public function updateMedia(Request $request, string $id)
    {
        try {
            $product = Product::findOrFail($id);
            $lastMediaItem = $product->media()->orderByDesc('display_order')->first();
            $displayOrder = $lastMediaItem ? $lastMediaItem->display_order : 0;

            if ($request->media !== null) {
                foreach ($request->media as $mediaItem) {
                    $imageName = StorageUtils::generateFileName($mediaItem);
                    $upload = $mediaItem->storeAs(ProductMedia::PHOTO_UPLOAD_DIRECTORY, $imageName, 'public');

                    if (!$upload) {
                        return back()->with('error', 'Error while uploading media files.');
                    }

                    $video = '';
                    $mime = $mediaItem->getClientMimeType();

                    if (strstr($mime, "video/")) {
                        $video = $mime;
                    }

                    $product->media()->create([
                        'media' => $imageName,
                        'video' => $video,
                        'display_order' => $displayOrder + 1
                    ]);

                    $displayOrder += 1;
                }
            }

            return back()->with('success', 'Updated successfully');
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Update media product', ['e' => $e]);

            return back()->with('error', 'An error occured');
        }
    }

    public function deleteMedia(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);
            $media = $product->media()->where('display_order', $request->input('key'))->first();
            $media->deletePhoto();
            $media->delete();
            $moveableMedia = $product->media()->where('display_order', '>', $request->input('key'))->get();
            $moveableMedia->each(function ($item) {
                $item->update([
                    'display_order' => $item->display_order - 1
                ]);
            });
            return response(200);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Delete media product', ['e' => $e]);
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function reorderMedia(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);

            foreach ($request->stack as $index => $stackItem) {
                $mediaFile = $product->media()->where('media', $stackItem['caption'])->first();
                $mediaFile->update(['display_order' => $index + 1]);
            }

            return response(200);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Reorder media product', ['e' => $e]);
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
