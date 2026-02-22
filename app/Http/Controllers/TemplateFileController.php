<?php

namespace App\Http\Controllers;

use App\Models\TemplateFile;
use App\Models\ProductionTemplate;
use App\Models\ShowCatalogue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TemplateFileController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:show_catalogues.update');
    }

    public function store(Request $request, ShowCatalogue $showCatalogue, ProductionTemplate $productionTemplate)
    {
        $data = $request->validate([
            'type' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'file' => 'required|file|max:20480',
            'notes' => 'nullable|string',
        ]);

        $file = $request->file('file');
        $path = $file->store('template_files/' . $productionTemplate->id, 'public');

        $productionTemplate->files()->create([
            'type' => $data['type'],
            'title' => $data['title'],
            'file_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()->to(route('show_catalogues.production_templates.show', [$showCatalogue, $productionTemplate]) . '?tab=files')
            ->with('success', 'File added.');
    }

    public function update(Request $request, ShowCatalogue $showCatalogue, ProductionTemplate $productionTemplate, TemplateFile $templateFile)
    {
        $data = $request->validate([
            'type' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'file' => 'nullable|file|max:20480',
            'notes' => 'nullable|string',
        ]);

        $update = [
            'type' => $data['type'],
            'title' => $data['title'],
            'notes' => $data['notes'] ?? null,
        ];

        if ($request->hasFile('file')) {
            Storage::disk('public')->delete($templateFile->file_path);
            $file = $request->file('file');
            $update['file_path'] = $file->store('template_files/' . $productionTemplate->id, 'public');
            $update['original_filename'] = $file->getClientOriginalName();
        }

        $templateFile->update($update);

        return redirect()->to(route('show_catalogues.production_templates.show', [$showCatalogue, $productionTemplate]) . '?tab=files')
            ->with('success', 'File updated.');
    }

    public function destroy(ShowCatalogue $showCatalogue, ProductionTemplate $productionTemplate, TemplateFile $templateFile)
    {
        Storage::disk('public')->delete($templateFile->file_path);
        $templateFile->delete();

        return redirect()->to(route('show_catalogues.production_templates.show', [$showCatalogue, $productionTemplate]) . '?tab=files')
            ->with('success', 'File deleted.');
    }

    public function download(ShowCatalogue $showCatalogue, ProductionTemplate $productionTemplate, TemplateFile $templateFile)
    {
        return Storage::disk('public')->download($templateFile->file_path, $templateFile->original_filename);
    }
}
