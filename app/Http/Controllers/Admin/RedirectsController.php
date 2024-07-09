<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Redirect;
use App\Services\LogChannelsEnum;
use App\Services\LoggerService;
use Exception;
use Illuminate\Http\Request;

class RedirectsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $redirects = Redirect::orderByDesc('created_at')->paginate(10);

        return view('admin.redirects.index')->with([
            'redirects' => $redirects
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $sourceUrl = $request->input('source_url');
            $targetUrl = $request->input('target_url');

            if ($sourceUrl[0] === '/') {
                $sourceUrl = ltrim($sourceUrl, '/');
            }

            if ($targetUrl[0] === '/') {
                $targetUrl = ltrim($targetUrl, '/');
            }

            Redirect::create([
                'source_url' => $sourceUrl,
                'target_url' => $targetUrl,
            ]);

            return back()->with('success', 'Created successfully');
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Store redirect', ['e' => $e]);

            return back()->with('error', __('app.error-occured'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $redirect = Redirect::findOrFail($id);
            $redirect->delete();

            return back()->with(['success' => 'Deleted.']);
        } catch (Exception $e) {
            LoggerService::logError(LogChannelsEnum::Application, 'Destroy redirect', ['e' => $e]);

            return back()->with(['error' => __('app.error-occured')]);
        }
    }
}
