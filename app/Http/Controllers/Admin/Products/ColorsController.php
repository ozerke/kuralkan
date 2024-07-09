<?php

namespace App\Http\Controllers\Admin\Products;

use App\Http\Controllers\Controller;
use App\Models\Color;
use App\Models\Language;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\ProductVariationMedia;
use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use App\Utils\StorageUtils;
use Exception;
use Illuminate\Http\Request;

class ColorsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $colors = Color::all();

            return view('admin.products.colors.index')->with([
                'colors' => $colors
            ]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Admin colors index', ['e' => $e]);
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
            $color = Color::findOrFail($id);

            return view('admin.products.colors.edit')->with([
                'color' => $color,
            ]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Admin colors edit', ['e' => $e]);

            return back()->with('error', 'An error occured');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $color = Color::findOrFail($id);

            $mediaItem = $request->color_image;

            if ($mediaItem !== null) {

                if ($color->color_image) {
                    $color->deleteColorImage();
                    $color->color_image = null;
                }

                $imageName = StorageUtils::generateFileName($mediaItem);
                $upload = $mediaItem->storeAs(Color::IMAGE_UPLOAD_DIRECTORY, $imageName, 'public');

                if (!$upload) {
                    return back()->with('error', 'Error while uploading image file.');
                }

                $color->color_image = $imageName;
                $color->save();
            }

            if ($request->input('color_name_en')) {
                $tranlation = $color->getTranslationByKey('en');

                if (!$tranlation) {
                    $tranlation = $color->translations()->create([
                        'color_name' => $request->input('color_name_en'),
                        'lang_id' => Language::AVAILABLE['en']
                    ]);
                } else {
                    $tranlation->update([
                        'color_name' => $request->input('color_name_en')
                    ]);
                }
            }

            return back()->with('success', 'Updated successfully');
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Admin colors update', ['e' => $e]);

            return back()->with('error', 'An error occured');
        }
    }

    public function deleteImage(Request $request, $id)
    {
        try {
            $color = Color::findOrFail($id);
            $color->deleteColorImage();
            $color->update([
                'color_image' => ''
            ]);
            return back()->with('success', 'Image was deleted successfully');
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Admin colors delete image', ['e' => $e]);
            return back()->with('error', 'An error occured');
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
