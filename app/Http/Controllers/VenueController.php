<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use Illuminate\Http\Request;

class VenueController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:venues.view')->only(['index','show']);
        $this->middleware('permission:venues.create')->only(['create','store']);
        $this->middleware('permission:venues.update')->only(['edit','update']);
        $this->middleware('permission:venues.delete')->only(['destroy']);
    }

    public function index()
    {
        $venues = Venue::paginate(20);
        return view('venues.index', compact('venues'));
    }

    public function create()
    {
        return view('venues.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:100',
            'website' => 'nullable|url',
            'notes' => 'nullable|string',
        ]);

        $venue = Venue::create($data);

        return redirect()->route('venues.show', $venue)->with('success','Venue created.');
    }

    public function show(Venue $venue)
    {
        $venue->load('buildings');
        $employments = \App\Models\Employment::where('employable_type', \App\Models\Venue::class)
            ->where('employable_id', $venue->id)
            ->with('contact')
            ->get();
        $contacts = \App\Models\Contact::orderBy('last_name')->get();
        return view('venues.show', compact('venue', 'employments', 'contacts'));
    }

    public function edit(Venue $venue)
    {
        return view('venues.edit', compact('venue'));
    }

    public function update(Request $request, Venue $venue)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:100',
            'website' => 'nullable|url',
            'notes' => 'nullable|string',
        ]);

        $venue->update($data);

        return redirect()->route('venues.show', $venue)->with('success','Venue updated.');
    }

    public function destroy(Venue $venue)
    {
        $venue->delete();
        return redirect()->route('venues.index')->with('success','Venue deleted.');
    }
}
