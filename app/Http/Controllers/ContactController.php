<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:contacts.view')->only(['index','show']);
        $this->middleware('permission:contacts.create')->only(['create','store']);
        $this->middleware('permission:contacts.update')->only(['edit','update']);
        $this->middleware('permission:contacts.delete')->only(['destroy']);
    }

    public function index()
    {
        $contacts = Contact::orderBy('last_name')->paginate(20);
        return view('contacts.index', compact('contacts'));
    }

    public function create()
    {
        return view('contacts.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:50',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'given_name' => 'nullable|string|max:255',
            'pronouns' => 'nullable|string|max:255',
            'locations' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $data['locations'] = $this->parseLocations($data['locations'] ?? null);

        Contact::create($data);

        return redirect()->route('contacts.index')->with('success', 'Contact created.');
    }

    public function show(Contact $contact)
    {
        $companies = \App\Models\Company::orderBy('name')->get();
        $employments = $contact->employments()->with('employable')->get();
        $emails = $contact->emails()->with(['company'])->get();
        $phones = $contact->phones()->with(['company'])->get();
        return view('contacts.show', compact('contact','companies','employments','emails','phones'));
    }

    public function edit(Contact $contact)
    {
        return view('contacts.edit', compact('contact'));
    }

    public function update(Request $request, Contact $contact)
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:50',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'given_name' => 'nullable|string|max:255',
            'pronouns' => 'nullable|string|max:255',
            'locations' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $data['locations'] = $this->parseLocations($data['locations'] ?? null);

        $contact->update($data);

        return redirect()->route('contacts.index')->with('success', 'Contact updated.');
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();
        return redirect()->route('contacts.index')->with('success', 'Contact deleted.');
    }

    protected function parseLocations($input)
    {
        if (empty($input)) {
            return null;
        }
        return array_values(array_filter(array_map('trim', explode(',', $input))));
    }
}
