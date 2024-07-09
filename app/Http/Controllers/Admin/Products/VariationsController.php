<?php

namespace App\Http\Controllers\Admin\Products;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\ProductVariationMedia;
use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use App\Utils\StorageUtils;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VariationsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);

            $variations = $product->variations()->get();

            return view('admin.products.variations')->with([
                'product' => $product,
                'variations' => $variations
            ]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Admin variations index', ['e' => $e]);
            return back()->with('error', 'Could not update the product');
        }
    }

    public function toggleDisplay(Request $request, $id)
    {
        try {
            $variation = ProductVariation::findOrFail($id);

            $variation->update([
                'display' => $variation->display === 'f' ? 't' : 'f'
            ]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Admin variations toggle display', ['e' => $e]);
            return back()->with('error', 'Could not update the variation');
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
            $variation = ProductVariation::findOrFail($id);
            $product = $variation->product;

            return view('admin.products.edit-variation')->with([
                'product' => $product,
                'variation' => $variation
            ]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Admin variations edit', ['e' => $e]);

            return back()->with('error', 'An error occured');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $variation = ProductVariation::findOrFail($id);
            $lastMediaItem = $variation->media()->orderByDesc('display_order')->first();
            $displayOrder = $lastMediaItem ? $lastMediaItem->display_order : 0;

            if ($request->media !== null) {
                foreach ($request->media as $mediaItem) {
                    $imageName = StorageUtils::generateFileName($mediaItem);
                    $upload = $mediaItem->storeAs(ProductVariationMedia::PHOTO_UPLOAD_DIRECTORY, $imageName, 'public');

                    if (!$upload) {
                        return back()->with('error', 'Error while uploading media files.');
                    }

                    $video = '';
                    $mime = $mediaItem->getClientMimeType();

                    if (strstr($mime, "video/")) {
                        $video = $mime;
                    }

                    $variation->media()->create([
                        'media' => $imageName,
                        'video' => $video,
                        'display_order' => $displayOrder + 1
                    ]);

                    $displayOrder += 1;
                }
            }

            $variation->update([
                'display' => $request->has('display') ? 't' : 'f',
                'display_order' => $request->input('display-order'),
                'price' => $request->input('price'),
                'is_notifiable' => 0
            ]);

            return back()->with('success', 'Updated successfully');
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Admin variations update', ['e' => $e]);

            return back()->with('error', 'An error occured');
        }
    }

    public function deleteMedia(Request $request, $id)
    {
        try {
            $variation = ProductVariation::findOrFail($id);
            $media = $variation->media()->where('display_order', $request->input('key'))->first();
            $media->deletePhoto();
            $media->delete();
            $moveableMedia = $variation->media()->where('display_order', '>', $request->input('key'))->get();
            $moveableMedia->each(function ($item) {
                $item->update([
                    'display_order' => $item->display_order - 1
                ]);
            });
            return response(200);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Admin variations delete media', ['e' => $e]);
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function reorderMedia(Request $request, $id)
    {
        try {
            $variation = ProductVariation::findOrFail($id);

            foreach ($request->stack as $index => $stackItem) {
                $mediaFile = $variation->media()->where('media', $stackItem['caption'])->first();
                $mediaFile->update(['display_order' => $index + 1]);
            }

            return response(200);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Admin variations reorder media', ['e' => $e]);
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

    public function shopStocksByVariation(Request $request, $id)
    {
        try {
            $variation = ProductVariation::findOrFail($id);

            $shopStocks = $variation->shopStocks;

            return view('admin.products.shop-stocks')->with([
                'variation' => $variation,
                'shopStocks' => $shopStocks,
                'totalStock' => $shopStocks->sum('stock')
            ]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Admin variations stocks by variation', ['e' => $e]);
            return back()->with('error', 'Could not get the products');
        }
    }

    public function orders(Request $request, $variationId)
    {
        try {
            $variation = ProductVariation::findOrFail($variationId);

            $orders = $variation->orders()->with(['productVariation.product', 'orderPayments']);

            if ($request->input('date-range')) {
                $range = explode(' - ', $request->input('date-range'));

                $range[0] = Carbon::parse($range[0])->format('Y-m-d 00:00');
                $range[1] = Carbon::parse($range[1])->format('Y-m-d 23:59');;

                if ($range[0] === $range[1]) {
                    $orders->whereDate('created_at', $range[0]);
                } else {
                    $orders->whereBetween('created_at', [$range[0], $range[1]]);
                }
            } else {
                $orders->whereDate('created_at', Carbon::today()->format('Y-m-d'));
            }

            $orderStatuses = OrderStatus::all();

            $translations = collect($orderStatuses);

            $awaiting = $translations->where('id', 1)->first();
            $confirmed = $translations->where('id', 2)->first();
            $supplying = $translations->where('id', 3)->first();
            $servicePoint = $translations->where('id', 4)->first();
            $delivered = $translations->where('id', 5)->first();

            $translations = [
                'awaiting' => $awaiting->currentTranslation->status,
                'confirmed' =>  $confirmed->currentTranslation->status,
                'supplying' =>  $supplying->currentTranslation->status,
                'servicePoint' =>  $servicePoint->currentTranslation->status,
                'delivered' =>  $delivered->currentTranslation->status,
            ];

            if ($request->input('status')) {
                $orders->whereHas('statusHistory', function ($q) use ($request) {
                    $q->latest()->take(1)->where('order_status_id', $request->input('status'));
                });
            }

            $orders = $orders->get();

            $statuses = $variation->orders()->whereHas('statusHistory')->get()->countBy(fn ($order) => $order->latest_status->order_status_id)->all();

            return view('admin.products.variations.orders')->with([
                'orders' => $orders,
                'awaiting' => $statuses[1] ?? 0,
                'confirmed' => $statuses[2] ?? 0,
                'supplying' => $statuses[3] ?? 0,
                'servicePoint' => $statuses[4] ?? 0,
                'delivered' => $statuses[5] ?? 0,
                'orderStatuses' => $orderStatuses,
                'translations' => $translations,
                'variation' => $variation
            ]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Admin variations orders', ['e' => $e]);

            return back()->with('error', 'Error ocurred');
        }
    }
}
