<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Space;
use Illuminate\Http\Request;

class SpaceController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:spaces.view')->only(['index','show']);
        $this->middleware('permission:spaces.create')->only(['create','store']);
        $this->middleware('permission:spaces.update')->only(['edit','update']);
        $this->middleware('permission:spaces.delete')->only(['destroy']);
    }

    public function index()
    {
        $spaces = Space::with('building.venue')->paginate(20);
        return view('spaces.index', compact('spaces'));
    }

    public function create()
    {
        $buildings = Building::all();
        return view('spaces.create', compact('buildings'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'building_id' => 'nullable|exists:buildings,id',
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $space = Space::create($data);

        return redirect()->route('spaces.show', $space)->with('success','Space created.');
    }

    public function show(Space $space)
    {
        $space->load('building.venue');
        $employments = \App\Models\Employment::where('employable_type', \App\Models\Space::class)
            ->where('employable_id', $space->id)
            ->with('contact')
            ->get();
        $contacts = \App\Models\Contact::orderBy('last_name')->get();
        return view('spaces.show', compact('space', 'employments', 'contacts'));
    }

    public function edit(Space $space)
    {
        $buildings = Building::all();
        return view('spaces.edit', compact('space','buildings'));
    }

    public function update(Request $request, Space $space)
    {
        $data = $request->validate([
            'building_id' => 'nullable|exists:buildings,id',
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $space->update($data);

        return redirect()->route('spaces.show', $space)->with('success','Space updated.');
    }

    public function destroy(Space $space)
    {
        $space->delete();
        return redirect()->route('spaces.index')->with('success','Space deleted.');
    }
}
