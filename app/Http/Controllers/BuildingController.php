<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Venue;
use Illuminate\Http\Request;

class BuildingController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:buildings.view')->only(['index','show']);
        $this->middleware('permission:buildings.create')->only(['create','store']);
        $this->middleware('permission:buildings.update')->only(['edit','update']);
        $this->middleware('permission:buildings.delete')->only(['destroy']);
    }

    public function index()
    {
        $buildings = Building::with('venue')->paginate(20);
        return view('buildings.index', compact('buildings'));
    }

    public function create()
    {
        $venues = Venue::all();
        return view('buildings.create', compact('venues'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'venue_id' => 'nullable|exists:venues,id',
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:100',
            'website' => 'nullable|url',
            'notes' => 'nullable|string',
        ]);

        $building = Building::create($data);

        return redirect()->route('buildings.show', $building)->with('success','Building created.');
    }

    public function show(Building $building)
    {
        $building->load('spaces','venue');
        $employments = \App\Models\Employment::where('employable_type', \App\Models\Building::class)
            ->where('employable_id', $building->id)
            ->with('contact')
            ->get();
        $contacts = \App\Models\Contact::orderBy('last_name')->get();
        return view('buildings.show', compact('building', 'employments', 'contacts'));
    }

    public function edit(Building $building)
    {
        $venues = Venue::all();
        return view('buildings.edit', compact('building','venues'));
    }

    public function update(Request $request, Building $building)
    {
        $data = $request->validate([
            'venue_id' => 'nullable|exists:venues,id',
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:100',
            'website' => 'nullable|url',
            'notes' => 'nullable|string',
        ]);

        $building->update($data);

        return redirect()->route('buildings.show', $building)->with('success','Building updated.');
    }

    public function destroy(Building $building)
    {
        $building->delete();
        return redirect()->route('buildings.index')->with('success','Building deleted.');
    }
}
