<?php

namespace App\Http\Controllers\Admin\Templates;

use App\Http\Controllers\Controller;
use App\Services\Templates\MailTemplateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class MailTemplateController extends Controller
{
    public function __construct(protected MailTemplateService $service)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $templates = $this->service->getTemplate();

        return view('admin.templates.mail.index')->with([
            'templates' => $templates
        ]);
    }

    public function edit($key)
    {
        if (!$key) {
            return redirect()->route('templates.mail.index');
        }

        $template = $this->service->getTemplate($key);

        $templatePath = resource_path('views/' . str_replace('.', '/', $template['file']) . '.blade.php');

        if (File::exists($templatePath)) {
            $content = File::get($templatePath);
        } else {
            $content = '';
        }

        return view('admin.templates.mail.edit', compact('template', 'key', 'content'));
    }

    public function update(Request $request, $key)
    {
        if (!$key) {
            return redirect()->route('templates.mail.index');
        }

        $template = $this->service->getTemplate($key);

        $templatePath = resource_path('views/' . str_replace('.', '/', $template['file']) . '.blade.php');
        $directoryPath = dirname($templatePath);

        if (!File::exists($directoryPath)) {
            File::makeDirectory($directoryPath, 0755, true);
        }

        File::put($templatePath, $request->input('content'));

        return redirect()->route('templates.mail.edit', $key)->with('success', __('app.updated-template'));
    }
}
