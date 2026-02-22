<?php

namespace App\Http\Controllers;

use App\Models\CataloguePaperwork;
use App\Models\ShowCatalogue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CataloguePaperworkController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:show_catalogues.update');
    }

    public function store(Request $request, ShowCatalogue $showCatalogue)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'file' => 'required|file|max:20480',
            'notes' => 'nullable|string',
        ]);

        $file = $request->file('file');
        $path = $file->store('catalogue_paperwork/' . $showCatalogue->id, 'public');

        $showCatalogue->paperwork()->create([
            'title' => $data['title'],
            'department' => $data['department'],
            'file_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()->to(route('show_catalogues.show', $showCatalogue) . '?tab=baseline-paperwork')
            ->with('success', 'Paperwork added.');
    }

    public function update(Request $request, ShowCatalogue $showCatalogue, CataloguePaperwork $paperwork)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'file' => 'nullable|file|max:20480',
            'notes' => 'nullable|string',
        ]);

        $update = [
            'title' => $data['title'],
            'department' => $data['department'],
            'notes' => $data['notes'] ?? null,
        ];

        if ($request->hasFile('file')) {
            Storage::disk('public')->delete($paperwork->file_path);
            $file = $request->file('file');
            $update['file_path'] = $file->store('catalogue_paperwork/' . $showCatalogue->id, 'public');
            $update['original_filename'] = $file->getClientOriginalName();
        }

        $paperwork->update($update);

        return redirect()->to(route('show_catalogues.show', $showCatalogue) . '?tab=baseline-paperwork')
            ->with('success', 'Paperwork updated.');
    }

    public function destroy(ShowCatalogue $showCatalogue, CataloguePaperwork $paperwork)
    {
        Storage::disk('public')->delete($paperwork->file_path);
        $paperwork->delete();

        return redirect()->to(route('show_catalogues.show', $showCatalogue) . '?tab=baseline-paperwork')
            ->with('success', 'Paperwork deleted.');
    }

    public function download(ShowCatalogue $showCatalogue, CataloguePaperwork $paperwork)
    {
        return Storage::disk('public')->download($paperwork->file_path, $paperwork->original_filename);
    }
}
