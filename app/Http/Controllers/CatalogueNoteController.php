<?php

namespace App\Http\Controllers;

use App\Models\CatalogueNote;
use App\Models\ShowCatalogue;
use Illuminate\Http\Request;

class CatalogueNoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:show_catalogues.update');
    }

    public function store(Request $request, ShowCatalogue $showCatalogue)
    {
        $data = $request->validate([
            'note_type' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'author' => 'nullable|string|max:255',
            'note_text' => 'required|string',
        ]);

        $showCatalogue->notes()->create($data);

        return redirect()->to(route('show_catalogues.show', $showCatalogue) . '?tab=baseline-notes')
            ->with('success', 'Note added.');
    }

    public function update(Request $request, ShowCatalogue $showCatalogue, CatalogueNote $note)
    {
        $data = $request->validate([
            'note_type' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'author' => 'nullable|string|max:255',
            'note_text' => 'required|string',
        ]);

        $note->update($data);

        return redirect()->to(route('show_catalogues.show', $showCatalogue) . '?tab=baseline-notes')
            ->with('success', 'Note updated.');
    }

    public function destroy(ShowCatalogue $showCatalogue, CatalogueNote $note)
    {
        $note->delete();

        return redirect()->to(route('show_catalogues.show', $showCatalogue) . '?tab=baseline-notes')
            ->with('success', 'Note deleted.');
    }
}
