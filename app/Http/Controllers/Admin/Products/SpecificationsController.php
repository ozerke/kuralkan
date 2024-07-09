<?php

namespace App\Http\Controllers\Admin\Products;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductTechnicalSpecification;
use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use Exception;
use Illuminate\Http\Request;

class SpecificationsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);

            $specifications = $product->specifications->groupBy('display_order');

            $specifications = $specifications->map(function ($group) {
                return [
                    $group[0]->language_key => $group[0],
                    $group[1]->language_key => $group[1],
                ];
            });

            return view('admin.products.specifications')->with([
                'product' => $product,
                'specifications' => $specifications
            ]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Admin specifications index', ['e' => $e]);
            return back()->with('error', 'Could not get the specifications');
        }
    }

    public function updateSpecification(Request $request, $id)
    {
        try {
            ['value' => $value] = $request->only(['value']);

            $specification = ProductTechnicalSpecification::findOrFail($id);
            $specification->update([
                'specification' => $value
            ]);

            return response(200);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Admin specifications update', ['e' => $e]);
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function updateValue(Request $request, $id)
    {
        try {
            ['value' => $value] = $request->only(['value']);

            $specification = ProductTechnicalSpecification::findOrFail($id);
            $specification->update([
                'value' => $value
            ]);

            return response(200);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Admin specifications update value', ['e' => $e]);
            return response()->json(['message' => $e->getMessage()]);
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
