<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Product;
use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use Exception;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $campaigns = Campaign::all();

        return view('admin.campaigns.index')->with(['campaigns' => $campaigns]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::all(['id', 'stock_code']);

        return view('admin.campaigns.create')->with(['products' => $products]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $productId = $request->input('product');
            $downPayment = $request->input('down_payment');
            $installments = $request->input('installments');
            $expCode = $request->input('bt_payment_exp_code');
            $rate = $request->input('rate');

            $product = Product::findOrFail($productId);

            $product->campaigns()->create([
                'down_payment' => $downPayment,
                'installments' => $installments,
                'bt_payment_exp_code' => $expCode,
                'rate' => $rate
            ]);

            return back()->with('success', 'Created successfully');
        } catch (UniqueConstraintViolationException $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Create campaign', ['e' => $e]);

            return back()->with('error', __('app.bt-code-is-used'));
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Create campaign', ['e' => $e]);

            return back()->with('error', 'An error occured');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $campaign = Campaign::findOrFail($id);

            $products = Product::all(['id', 'stock_code']);

            return view('admin.campaigns.edit')->with([
                'campaign' => $campaign,
                'products' => $products,
            ]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Edit campaign', ['e' => $e]);
            return back()->with(['error' => 'Could not edit the campaign.']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $campaign = Campaign::findOrFail($id);

            $productId = $request->input('product');
            $downPayment = $request->input('down_payment');
            $installments = $request->input('installments');
            $rate = $request->input('rate');
            $expCode = $request->input('bt_payment_exp_code');

            $product = Product::findOrFail($productId);

            $campaign->update([
                'product_id' => $product->id,
                'down_payment' => $downPayment,
                'installments' => $installments,
                'bt_payment_exp_code' => $expCode,
                'rate' => $rate
            ]);

            return back()->with('success', 'Updated successfully');
        } catch (UniqueConstraintViolationException $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Create campaign', ['e' => $e]);

            return back()->with('error',  __('app.bt-code-is-used'));
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Update campaign', ['e' => $e]);

            return back()->with('error', 'An error occured');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $campaign = Campaign::findOrFail($id);
            $campaign->delete();
            return back()->with(['success' => 'Deleted.']);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Destroy campaign', ['e' => $e]);
            return back()->with(['error' => 'Could not delete the campaign.']);
        }
    }
}
