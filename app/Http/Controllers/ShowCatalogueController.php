<?php

namespace App\Http\Controllers;

use App\Models\ShowCatalogue;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ShowCatalogueController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:show_catalogues.view')->only(['index','show']);
        $this->middleware('permission:show_catalogues.create')->only(['create','store']);
        $this->middleware('permission:show_catalogues.update')->only(['edit','update']);
        $this->middleware('permission:show_catalogues.delete')->only(['destroy']);
    }

    public function index()
    {
        $catalogues = ShowCatalogue::orderByDesc('created_at')->get();
        return view('show_catalogues.index', compact('catalogues'));
    }

    public function create()
    {
        return view('show_catalogues.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'version' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'choreography_by' => 'nullable|string|max:255',
            'created_at' => 'nullable|date',
        ]);

        $catalogue = ShowCatalogue::create($data);

        if ($request->filled('created_at')) {
            $catalogue->created_at = Carbon::parse($request->input('created_at'));
            $catalogue->save();
        }

        return redirect()->route('show_catalogues.index')->with('success','Catalogue entry created.');
    }

    public function show(ShowCatalogue $showCatalogue)
    {
        return view('show_catalogues.show', compact('showCatalogue'));
    }

    public function edit(ShowCatalogue $showCatalogue)
    {
        return view('show_catalogues.edit', compact('showCatalogue'));
    }

    public function update(Request $request, ShowCatalogue $showCatalogue)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'version' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'choreography_by' => 'nullable|string|max:255',
            'created_at' => 'nullable|date',
        ]);

        $showCatalogue->update($data);
        if ($request->filled('created_at')) {
            $showCatalogue->created_at = Carbon::parse($request->input('created_at'));
            $showCatalogue->save();
        }

        return redirect()->route('show_catalogues.show', $showCatalogue)->with('success','Catalogue entry updated.');
    }

    public function destroy(ShowCatalogue $showCatalogue)
    {
        $showCatalogue->delete();
        return redirect()->route('show_catalogues.index')->with('success','Catalogue entry deleted.');
    }
}
