<?php

namespace App\Http\Controllers;

use App\Models\TemplateStaffing;
use App\Models\ProductionTemplate;
use App\Models\ShowCatalogue;
use Illuminate\Http\Request;

class TemplateStaffingController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:show_catalogues.update');
    }

    public function store(Request $request, ShowCatalogue $showCatalogue, ProductionTemplate $productionTemplate)
    {
        $data = $request->validate([
            'department' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $productionTemplate->staffing()->create($data);

        return redirect()->to(route('show_catalogues.production_templates.show', [$showCatalogue, $productionTemplate]) . '?tab=baseline-staffing')
            ->with('success', 'Staffing line added.');
    }

    public function update(Request $request, ShowCatalogue $showCatalogue, ProductionTemplate $productionTemplate, TemplateStaffing $staffing)
    {
        $data = $request->validate([
            'department' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $staffing->update($data);

        return redirect()->to(route('show_catalogues.production_templates.show', [$showCatalogue, $productionTemplate]) . '?tab=baseline-staffing')
            ->with('success', 'Staffing line updated.');
    }

    public function destroy(ShowCatalogue $showCatalogue, ProductionTemplate $productionTemplate, TemplateStaffing $staffing)
    {
        $staffing->delete();

        return redirect()->to(route('show_catalogues.production_templates.show', [$showCatalogue, $productionTemplate]) . '?tab=baseline-staffing')
            ->with('success', 'Staffing line deleted.');
    }
}
