<?php

namespace App\Http\Controllers;

use App\Models\Show;
use App\Models\Season;
use App\Models\Space;
use App\Models\Company;
use App\Models\Contact;
use App\Models\ShowCatalogue;
use Illuminate\Http\Request;

class ShowController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:shows.view')->only(['index','show']);
        $this->middleware('permission:shows.create')->only(['create','store']);
        $this->middleware('permission:shows.update')->only(['edit','update']);
        $this->middleware('permission:shows.delete')->only(['destroy']);
    }
    public function index()
    {
        $shows = Show::with(['season'])->orderByDesc('opening_date')->get();
        return view('shows.index', compact('shows'));
    }

    public function create()
    {
        $seasons = Season::orderByDesc('start_date')->get();
        $catalogues = ShowCatalogue::orderBy('title')->get();
        return view('shows.create', compact('seasons','catalogues'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'season_id' => 'required|exists:seasons,id',
            'show_catalogue_id' => 'nullable|exists:show_catalogues,id',
            'title' => 'required|string|max:255',
            'opening_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'choreography_by' => 'nullable|string|max:255',
        ]);

        $data['status'] = 'New';

        $show = Show::create($data);

        return redirect()->route('shows.show', $show)->with('success','Show created.');
    }

    public function show(Show $show)
    {
        $show->load(['season','productions.companies','productions.contacts']);

        $companies = Company::orderBy('name')->get();
        $contacts = Contact::orderBy('last_name')->get();

        return view('shows.show', compact('show','companies','contacts'));
    }

    public function edit(Show $show)
    {
        $seasons = Season::orderByDesc('start_date')->get();
        $catalogues = ShowCatalogue::orderBy('title')->get();
        $show->load([]);
        return view('shows.edit', compact('show','seasons','catalogues'));
    }

    public function update(Request $request, Show $show)
    {
        $data = $request->validate([
            'season_id' => 'required|exists:seasons,id',
            'show_catalogue_id' => 'nullable|exists:show_catalogues,id',
            'title' => 'required|string|max:255',
            'opening_date' => 'nullable|date',
            'status' => 'required|string|max:50',
            'notes' => 'nullable|string',
            'choreography_by' => 'nullable|string|max:255',
        ]);

        $show->update($data);

        return redirect()->route('shows.show', $show)->with('success','Show updated.');
    }

    public function destroy(Show $show)
    {
        $show->delete();
        return redirect()->route('shows.index')->with('success','Show deleted.');
    }
}
