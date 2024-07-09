<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use App\Models\Language;
use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use App\Utils\StorageUtils;
use Exception;
use Illuminate\Http\Request;

class HeroSlidesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $slides = HeroSlide::all();

        return view('admin.hero-slides.index')->with([
            'slides' => $slides
        ]);
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
            if ($request->media !== null) {
                $mediaItem = $request->media;

                $imageName = StorageUtils::generateFileName($mediaItem);
                $upload = $mediaItem->storeAs(HeroSlide::PHOTO_UPLOAD_DIRECTORY, $imageName, 'public');

                if (!$upload) {
                    return back()->with('error', 'Error while uploading media files.');
                }

                HeroSlide::create([
                    'title' => $request->input('title'),
                    'media' => $imageName,
                    'lang_id' => Language::AVAILABLE[$request->input('lang')] ?? 1,
                    'display_order' => $request->input('display_order'),
                    'url' => $request->input('url'),
                ]);
            }

            return back()->with('success', 'Created successfully');
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Create hero slide', ['e' => $e]);

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
            $slide = HeroSlide::findOrFail($id);

            return view('admin.hero-slides.edit')->with([
                'slide' => $slide,
            ]);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Edit hero slide', ['e' => $e]);
            return back()->with(['error' => 'Could not edit the slide.']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $slide = HeroSlide::findOrFail($id);

            $title = $request->input('title');
            $url = $request->input('url');
            $lang = $request->input('lang');
            $displayOrder = $request->input('display_order');

            $slide->update([
                'title' => $title,
                'display_order' => $displayOrder,
                'url' => $url,
                'lang_id' => Language::AVAILABLE[$lang] ?? 1,
            ]);

            return back()->with('success', 'Updated successfully');
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Update hero slide', ['e' => $e]);

            return back()->with('error', 'An error occured');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $slide = HeroSlide::findOrFail($id);
            $slide->deletePhoto();
            $slide->delete();
            return back()->with(['success' => 'Deleted.']);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Destroy hero slide', ['e' => $e]);
            return back()->with(['error' => 'Could not delete the media item.']);
        }
    }
}
