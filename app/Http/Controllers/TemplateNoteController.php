<?php

namespace App\Http\Controllers;

use App\Models\TemplateNote;
use App\Models\ProductionTemplate;
use App\Models\ShowCatalogue;
use Illuminate\Http\Request;

class TemplateNoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:show_catalogues.update');
    }

    public function store(Request $request, ShowCatalogue $showCatalogue, ProductionTemplate $productionTemplate)
    {
        $data = $request->validate([
            'note_type' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'author' => 'nullable|string|max:255',
            'note_text' => 'required|string',
        ]);

        $productionTemplate->templateNotes()->create($data);

        return redirect()->to(route('show_catalogues.production_templates.show', [$showCatalogue, $productionTemplate]) . '?tab=baseline-notes')
            ->with('success', 'Note added.');
    }

    public function update(Request $request, ShowCatalogue $showCatalogue, ProductionTemplate $productionTemplate, TemplateNote $note)
    {
        $data = $request->validate([
            'note_type' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'author' => 'nullable|string|max:255',
            'note_text' => 'required|string',
        ]);

        $note->update($data);

        return redirect()->to(route('show_catalogues.production_templates.show', [$showCatalogue, $productionTemplate]) . '?tab=baseline-notes')
            ->with('success', 'Note updated.');
    }

    public function destroy(ShowCatalogue $showCatalogue, ProductionTemplate $productionTemplate, TemplateNote $note)
    {
        $note->delete();

        return redirect()->to(route('show_catalogues.production_templates.show', [$showCatalogue, $productionTemplate]) . '?tab=baseline-notes')
            ->with('success', 'Note deleted.');
    }
}
