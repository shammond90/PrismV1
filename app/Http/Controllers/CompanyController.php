<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:companies.view')->only(['index','show']);
        $this->middleware('permission:companies.create')->only(['create','store']);
        $this->middleware('permission:companies.update')->only(['edit','update']);
        $this->middleware('permission:companies.delete')->only(['destroy']);
    }

    public function index()
    {
        $companies = Company::orderBy('name')->paginate(20);
        return view('companies.index', compact('companies'));
    }

    public function create()
    {
        return view('companies.create');
    }

    public function store(Request $request)
    {
        // normalize website to include scheme so Laravel's URL validator accepts it
        $website = $request->input('website');
        if (!empty($website) && !preg_match('#^https?://#i', $website)) {
            $request->merge(['website' => 'http://'.$website]);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'industry' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'notes' => 'nullable|string',
        ]);

        Company::create($data);

        return redirect()->route('companies.index')->with('success', 'Company created.');
    }

    public function show(Company $company)
    {
        $contacts = \App\Models\Contact::orderBy('last_name')->get();
        $employments = $company->employments()->with(['contact.phones'])->get();
        return view('companies.show', compact('company','contacts','employments'));
    }

    public function edit(Company $company)
    {
        return view('companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        // normalize website to include scheme so Laravel's URL validator accepts it
        $website = $request->input('website');
        if (!empty($website) && !preg_match('#^https?://#i', $website)) {
            $request->merge(['website' => 'http://'.$website]);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'industry' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'notes' => 'nullable|string',
        ]);

        $company->update($data);

        return redirect()->route('companies.index')->with('success', 'Company updated.');
    }

    public function destroy(Company $company)
    {
        $company->delete();
        return redirect()->route('companies.index')->with('success', 'Company deleted.');
    }
}
