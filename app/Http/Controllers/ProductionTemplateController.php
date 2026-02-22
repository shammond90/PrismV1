<?php

namespace App\Http\Controllers;

use App\Models\ProductionTemplate;
use App\Models\ShowCatalogue;
use Illuminate\Http\Request;

class ProductionTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:show_catalogues.update');
    }

    public function store(Request $request, ShowCatalogue $showCatalogue)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $template = $showCatalogue->productionTemplates()->create($data);

        // Redirect to the new template detail page
        return redirect()->route('show_catalogues.production_templates.show', [$showCatalogue, $template])
            ->with('success', 'Production Template created.');
    }

    public function show(ShowCatalogue $showCatalogue, ProductionTemplate $productionTemplate)
    {
        $productionTemplate->load('paperwork', 'templateNotes', 'staffing', 'schedules', 'files');
        $departments = \App\Models\Department::orderBy('name')->get();

        return view('show_catalogues.production_templates.show', compact('showCatalogue', 'productionTemplate', 'departments'));
    }

    public function update(Request $request, ShowCatalogue $showCatalogue, ProductionTemplate $productionTemplate)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $productionTemplate->update($data);

        return redirect()->route('show_catalogues.production_templates.show', [$showCatalogue, $productionTemplate])
            ->with('success', 'Production Template updated.');
    }

    public function destroy(ShowCatalogue $showCatalogue, ProductionTemplate $productionTemplate)
    {
        $productionTemplate->delete();

        return redirect()->to(route('show_catalogues.show', $showCatalogue) . '?tab=production-catalogue')
            ->with('success', 'Production Template deleted.');
    }
}
