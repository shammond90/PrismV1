<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use App\Models\Building;
use App\Models\Space;
use Illuminate\Http\Request;

class LocationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:venues.view|buildings.view|spaces.view')->only(['index']);
    }

    public function index()
    {
        // Keep hierarchical data available for cascading view
        $venues = Venue::with(['primaryAddress', 'buildings.spaces.primaryAddress'])->get();
        $orphanBuildings = Building::whereNull('venue_id')->with(['primaryAddress','spaces.primaryAddress'])->get();
        $orphanSpaces = Space::whereNull('building_id')->with('primaryAddress','building')->get();

        return view('locations.index', compact('venues','orphanBuildings','orphanSpaces'));
    }
}
