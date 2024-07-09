<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PublicFile;
use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use App\Utils\StorageUtils;
use Exception;
use Illuminate\Http\Request;

class PublicFilesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $files = PublicFile::orderByDesc('created_at')->paginate(10);

        return view('admin.public-files.index')->with([
            'files' => $files
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            if ($request->file !== null) {
                $file = $request->file;

                $fileName = StorageUtils::generateFileName($file);
                $upload = $file->storeAs(PublicFile::UPLOAD_DIRECTORY, $fileName, 'public');

                if (!$upload) {
                    return back()->with('error', __('app.error-occured'));
                }

                PublicFile::create([
                    'file' => $fileName,
                ]);
            }

            return back()->with('success', 'Uploaded successfully');
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Store public file', ['e' => $e]);

            return back()->with('error', __('app.error-occured'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $file = PublicFile::findOrFail($id);
            $file->deleteFile();
            $file->delete();

            return back()->with(['success' => 'Deleted.']);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Destroy public file', ['e' => $e]);
            return back()->with(['error' => __('app.error-occured')]);
        }
    }
}
