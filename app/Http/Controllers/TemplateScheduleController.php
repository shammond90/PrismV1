<?php

namespace App\Http\Controllers;

use App\Models\TemplateSchedule;
use App\Models\ProductionTemplate;
use App\Models\ShowCatalogue;
use Illuminate\Http\Request;

class TemplateScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:show_catalogues.update');
    }

    public function store(Request $request, ShowCatalogue $showCatalogue, ProductionTemplate $productionTemplate)
    {
        $data = $request->validate([
            'template_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'schedule_data' => 'nullable|string',
        ]);

        $productionTemplate->schedules()->create($data);

        return redirect()->to(route('show_catalogues.production_templates.show', [$showCatalogue, $productionTemplate]) . '?tab=baseline-schedules')
            ->with('success', 'Schedule template added.');
    }

    public function update(Request $request, ShowCatalogue $showCatalogue, ProductionTemplate $productionTemplate, TemplateSchedule $schedule)
    {
        $data = $request->validate([
            'template_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'schedule_data' => 'nullable|string',
        ]);

        $schedule->update($data);

        return redirect()->to(route('show_catalogues.production_templates.show', [$showCatalogue, $productionTemplate]) . '?tab=baseline-schedules')
            ->with('success', 'Schedule template updated.');
    }

    public function destroy(ShowCatalogue $showCatalogue, ProductionTemplate $productionTemplate, TemplateSchedule $schedule)
    {
        $schedule->delete();

        return redirect()->to(route('show_catalogues.production_templates.show', [$showCatalogue, $productionTemplate]) . '?tab=baseline-schedules')
            ->with('success', 'Schedule template deleted.');
    }
}
