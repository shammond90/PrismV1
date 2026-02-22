<?php

namespace App\Http\Controllers;

use App\Models\CatalogueFile;
use App\Models\ShowCatalogue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CatalogueFileController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:show_catalogues.update');
    }

    public function store(Request $request, ShowCatalogue $showCatalogue)
    {
        $data = $request->validate([
            'type' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'file' => 'required|file|max:20480',
            'notes' => 'nullable|string',
        ]);

        $file = $request->file('file');
        $path = $file->store('catalogue_files/' . $showCatalogue->id, 'public');

        $showCatalogue->files()->create([
            'type' => $data['type'],
            'title' => $data['title'],
            'file_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()->to(route('show_catalogues.show', $showCatalogue) . '?tab=files')
            ->with('success', 'File added.');
    }

    public function update(Request $request, ShowCatalogue $showCatalogue, CatalogueFile $catalogueFile)
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
            Storage::disk('public')->delete($catalogueFile->file_path);
            $file = $request->file('file');
            $update['file_path'] = $file->store('catalogue_files/' . $showCatalogue->id, 'public');
            $update['original_filename'] = $file->getClientOriginalName();
        }

        $catalogueFile->update($update);

        return redirect()->to(route('show_catalogues.show', $showCatalogue) . '?tab=files')
            ->with('success', 'File updated.');
    }

    public function destroy(ShowCatalogue $showCatalogue, CatalogueFile $catalogueFile)
    {
        Storage::disk('public')->delete($catalogueFile->file_path);
        $catalogueFile->delete();

        return redirect()->to(route('show_catalogues.show', $showCatalogue) . '?tab=files')
            ->with('success', 'File deleted.');
    }

    public function download(ShowCatalogue $showCatalogue, CatalogueFile $catalogueFile)
    {
        return Storage::disk('public')->download($catalogueFile->file_path, $catalogueFile->original_filename);
    }
}
