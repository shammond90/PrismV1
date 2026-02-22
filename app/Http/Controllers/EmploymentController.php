<?php

namespace App\Http\Controllers;

use App\Models\Employment;
use Illuminate\Http\Request;

class EmploymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:employments.create')->only(['store']);
        $this->middleware('permission:employments.update')->only(['update']);
        $this->middleware('permission:employments.delete')->only(['destroy']);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'contact_id' => 'required|exists:contacts,id',
            'employable_type' => 'required|string|in:company,venue,building,space',
            'employable_id' => 'required|integer',
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        // Map short type to full class name
        $typeMap = [
            'company' => \App\Models\Company::class,
            'venue' => \App\Models\Venue::class,
            'building' => \App\Models\Building::class,
            'space' => \App\Models\Space::class,
        ];

        $employableClass = $typeMap[$data['employable_type']];

        // Validate the employable exists
        if (!$employableClass::find($data['employable_id'])) {
            return back()->withInput()->withErrors(['employable_id' => 'The selected employer does not exist.']);
        }

        $employment = Employment::create([
            'contact_id' => $data['contact_id'],
            'employable_type' => $employableClass,
            'employable_id' => $data['employable_id'],
            'company_id' => $data['employable_type'] === 'company' ? $data['employable_id'] : null,
            'position' => $data['position'] ?? null,
            'department' => $data['department'] ?? null,
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
        ]);

        $return = $request->input('return_to') ?? url()->previous();
        return redirect($return)->with('employment_action', 'added');
    }

    public function update(Request $request, Employment $employment)
    {
        $data = $request->validate([
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $employment->update($data);

        $return = $request->input('return_to') ?? url()->previous();
        return redirect($return)->with('employment_action', 'updated');
    }

    public function destroy(Employment $employment)
    {
        $return = request()->input('return_to') ?? url()->previous();
        $employment->delete();
        return redirect($return)->with('employment_action', 'deleted');
    }

    /**
     * AJAX endpoint to search employables by type.
     */
    public function searchEmployables(Request $request)
    {
        $type = $request->query('type', 'all');
        $q = $request->query('q', '');

        $typeMap = [
            'company' => \App\Models\Company::class,
            'venue' => \App\Models\Venue::class,
            'building' => \App\Models\Building::class,
            'space' => \App\Models\Space::class,
        ];

        $results = [];

        if ($type === 'all') {
            // Search all types
            foreach ($typeMap as $shortType => $modelClass) {
                $items = $this->searchModel($modelClass, $q, $shortType);
                $results = array_merge($results, $items);
            }
        } elseif (isset($typeMap[$type])) {
            $results = $this->searchModel($typeMap[$type], $q, $type);
        }

        return response()->json(['results' => $results]);
    }

    private function searchModel(string $modelClass, string $q, string $type): array
    {
        $query = $modelClass::query();

        if ($q !== '') {
            $query->where('name', 'like', "%{$q}%");
        }

        $items = $query->limit(25)->get();

        return $items->map(function($m) use ($type) {
            $display = $m->name;
            // For spaces, prefix with building name when available
            if ($type === 'space' && isset($m->building) && $m->building) {
                $display = $m->building->name . ' - ' . $m->name;
            }

            return [
                'id' => $m->id,
                'name' => $m->name,
                'display_name' => $display,
                'type' => $type,
                'type_label' => ucfirst($type),
            ];
        })->toArray();
    }
}
