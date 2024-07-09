<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Configuration;
use App\Models\MenuSection;
use App\Models\Product;
use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use Exception;
use Illuminate\Http\Request;

class MenuSectionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sections = MenuSection::all();
        $categories = Category::all();

        return view('admin.menu-sections.index')->with(['sections' => $sections, 'categories' => $categories]);
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
        try {
            $brand = $request->input('product_brand');
            $titleTr = $request->input('title_tr');
            $titleEn = $request->input('title_en');
            $displayOrder = $request->input('display_order');
            $categoryId = $request->input('category_id');

            if (!in_array($brand, ['Kanuni', 'Bajaj'])) {
                return back()->with('error', 'Brand not found: ' . $brand);
            }

            $section = MenuSection::create([
                'product_brand' => $brand,
                'display_order' => $displayOrder,
                'category_id' => $categoryId
            ]);

            if ($section) {
                $section->translations()->create([
                    'lang_id' => 1,
                    'title' => $titleTr
                ]);

                $section->translations()->create([
                    'lang_id' => 2,
                    'title' => $titleEn
                ]);
            }

            return back()->with('success', 'Created successfully');
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Create menu section', ['e' => $e]);

            return back()->with('error', 'An error occured');
        }
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
            $section = MenuSection::findOrFail($id);

            $brandName = $section->product_brand;

            $brandProducts = Product::whereBrandName($brandName)->get();

            $categories = Category::all();

            return view('admin.menu-sections.edit')->with([
                'section' => $section,
                'products' => $brandProducts,
                'categories' => $categories
            ]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Edit menu section', ['e' => $e]);
            return back()->with(['error' => 'Could not edit the section.']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $section = MenuSection::findOrFail($id);

            $brand = $request->input('product_brand');
            $titleTr = $request->input('title_tr');
            $titleEn = $request->input('title_en');
            $displayOrder = $request->input('display_order');
            $categoryId = $request->input('category_id');
            $menuItems = $request->input('menu_items') ?? [];
            $itemOrder = $request->input('item_order') ?? [];

            if ($section) {
                $section->update([
                    'product_brand' => $brand,
                    'display_order' => $displayOrder,
                    'category_id' => $categoryId
                ]);

                $section->translations()->where('lang_id', 1)->update(['title' => $titleTr]);

                $section->translations()->where('lang_id', 2)->update(['title' => $titleEn]);

                $section->menuSectionItems()->delete();

                foreach ($menuItems as $value) {
                    $section->menuSectionItems()->create(['product_id' => $value]);
                }

                foreach ($itemOrder as $index => $item) {
                    $section->menuSectionItems()->where('product_id', $item)->update([
                        'display_order' => $index
                    ]);
                }
            }

            return back()->with('success', 'Updated successfully');
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Update menu section', ['e' => $e]);

            return back()->with('error', 'An error occured');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $section = MenuSection::findOrFail($id);
            $section->delete();
            return back()->with(['success' => 'Deleted.']);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Destroy menu section', ['e' => $e]);
            return back()->with(['error' => 'Could not delete the section.']);
        }
    }
}
