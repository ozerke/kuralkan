<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Models\Language;
use App\Models\Product;
use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use Exception;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();

        return view('admin.categories.index')->with([
            'categories' => $categories,
            'count' => count($categories)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $category = Category::create([
                'display_order' => $request->input('display-order'),
            ]);

            $category->translations()->create([
                'lang_id' => Language::AVAILABLE['tr'],
                'category_name' => $request->input('tr-category-name') ?? '',
                'slug' => $request->input('tr-slug') ?? '',
                'seo_title' => $request->input('tr-seo-title') ?? '',
                'seo_description' => $request->input('tr-seo-description') ?? '',
                'seo_keywords' => $request->input('tr-seo-keywords') ?? '',
                'description' => $request->input('tr-description') ?? null,
            ]);

            $category->translations()->create([
                'lang_id' => Language::AVAILABLE['en'],
                'category_name' => $request->input('en-category-name') ?? '',
                'slug' => $request->input('en-slug') ?? '',
                'seo_title' => $request->input('en-seo-title') ?? '',
                'seo_description' => $request->input('en-seo-description') ?? '',
                'seo_keywords' => $request->input('en-seo-keywords') ?? '',
                'description' => $request->input('en-description') ?? null,
            ]);

            return back()->with('success', 'Created successfully');
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Store category', ['e' => $e]);

            return back()->with('error', 'An error occured');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $category = Category::findOrFail($id);

            return view('admin.categories.edit')->with([
                'category' => $category,
            ]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Edit category', ['e' => $e]);

            return back()->with('error', 'An error occured');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $category = Category::findOrFail($id);

            $en = $category->en;
            $tr = $category->tr;

            $category->update([
                'display_order' => $request->input('display-order'),
            ]);

            $tr->update([
                'category_name' => $request->input('tr-category-name') ?? '',
                'slug' => $request->input('tr-slug') ?? '',
                'seo_title' => $request->input('tr-seo-title') ?? '',
                'seo_description' => $request->input('tr-seo-description') ?? '',
                'seo_keywords' => $request->input('tr-seo-keywords') ?? '',
                'description' => $request->input('tr-description') ?? null,
            ]);

            if ($en) {
                $en->update([
                    'category_name' => $request->input('en-category-name') ?? '',
                    'slug' => $request->input('en-slug') ?? '',
                    'seo_title' => $request->input('en-seo-title') ?? '',
                    'seo_description' => $request->input('en-seo-description') ?? '',
                    'seo_keywords' => $request->input('en-seo-keywords') ?? '',
                    'description' => $request->input('en-description') ?? null,
                ]);
            } else {
                $category->translations()->create([
                    'lang_id' => Language::AVAILABLE['en'],
                    'category_name' => $request->input('en-category-name') ?? '',
                    'slug' => $request->input('en-slug') ?? '',
                    'seo_title' => $request->input('en-seo-title') ?? '',
                    'seo_description' => $request->input('en-seo-description') ?? '',
                    'seo_keywords' => $request->input('en-seo-keywords') ?? '',
                    'description' => $request->input('en-description') ?? null,
                ]);
            }

            return back()->with('success', 'Updated successfully');
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Update category', ['e' => $e]);

            return back()->with('error', 'An error occured');
        }
    }

    public function updateCategory(Request $request, $id)
    {
        try {
            $translation = CategoryTranslation::findOrFail($id);

            $translation->update([
                'category_name' => $request->input('value')
            ]);

            return response(200);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Update category', ['e' => $e]);
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function updateSlug(Request $request, $id)
    {
        try {
            $translation = CategoryTranslation::findOrFail($id);

            $translation->update([
                'slug' => $request->input('value')
            ]);

            return response(200);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Update category slug', ['e' => $e]);
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function updateDisplayOrder(Request $request, $id)
    {
        try {
            $category = Category::findOrFail($id);

            $category->update([
                'display_order' => $request->input('value')
            ]);

            return response(200);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Update category display order', ['e' => $e]);
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
