<?php

namespace App\Http\Controllers;

use App\Models\Production;
use App\Models\Show;
use App\Models\Company;
use App\Models\Contact;
use Illuminate\Http\Request;

class ProductionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:productions.view')->only(['index','show']);
        $this->middleware('permission:productions.create')->only(['create','store']);
        $this->middleware('permission:productions.update')->only(['edit','update']);
        $this->middleware('permission:productions.delete')->only(['destroy']);
    }

    public function index()
    {
        $productions = Production::with('show')->orderByDesc('start_date')->get();
        return view('productions.index', compact('productions'));
    }

    public function create()
    {
        $shows = Show::orderByDesc('opening_date')->get();
        $companies = Company::orderBy('name')->get();
        $contacts = Contact::orderBy('last_name')->get();
        $venues = \App\Models\Venue::orderBy('name')->get();
        $buildings = \App\Models\Building::orderBy('name')->get();
        $spaces = \App\Models\Space::with('building')->orderBy('name')->get();
        return view('productions.create', compact('shows','companies','contacts','venues','buildings','spaces'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'show_id' => 'required|exists:shows,id',
            'title' => 'required|string|max:255',
            'status' => 'nullable|in:New,In Production,Open,Closed,Notes,Cancelled',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'initial_contact_date' => 'nullable|date',
            'space_id' => 'nullable|exists:spaces,id',
            'notes' => 'nullable|string',
            'primary_company_id' => 'nullable|exists:companies,id',
            'primary_contact_id' => 'nullable|exists:contacts,id',
        ]);

        // Ensure newly created productions always start with status 'New'
        $data['status'] = 'New';

        $prod = Production::create($data);
        // maintain pivot for primary selections for compatibility
        if (!empty($data['primary_company_id'])) {
            $prod->companies()->sync([$data['primary_company_id']]);
        }
        if (!empty($data['primary_contact_id'])) {
            $prod->contacts()->sync([$data['primary_contact_id']]);
        }

        return redirect()->route('productions.show', $prod)->with('success','Production created.');
    }

    public function show(Production $production)
    {
        $production->load('companies','contacts.emails','contacts.phones','show','space','primaryContact.emails','primaryContact.phones','primaryCompany');
        $companies = Company::orderBy('name')->get();
        $contacts = Contact::with('emails','phones')->orderBy('last_name')->get();
        $events = $production->events()->with('space')->orderBy('start_at')->get();
        $departments = \App\Models\Department::with('positions')->orderBy('name')->get();
        $eventTypes = \App\Models\EventType::orderBy('name')->get();
        $venues = \App\Models\Venue::orderBy('name')->get();
        $buildings = \App\Models\Building::orderBy('name')->get();
        $spaces = \App\Models\Space::with('building')->orderBy('name')->get();
        return view('productions.show', compact('production','companies','contacts','events','venues','buildings','spaces','departments','eventTypes'));
    }

    public function attachCompany(Request $request, Production $production)
    {
        if (!auth()->user()->can('productions.update')) {
            abort(403);
        }

        $data = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'primary' => 'nullable|boolean',
        ]);

        $production->companies()->syncWithoutDetaching([$data['company_id']]);
        if (!empty($data['primary'])) {
            $production->primary_company_id = $data['company_id'];
            $production->save();
        }

        return redirect()->to(route('productions.show', $production) . '#companies')->with('success', 'Company attached.');
    }

    public function detachCompany(Production $production, $companyId)
    {
        if (!auth()->user()->can('productions.update')) {
            abort(403);
        }

        $production->companies()->detach($companyId);
        if ($production->primary_company_id == $companyId) {
            $production->primary_company_id = null;
            $production->save();
        }

        return redirect()->to(route('productions.show', $production) . '#companies')->with('success', 'Company removed.');
    }

    public function attachContact(Request $request, Production $production)
    {
        if (!auth()->user()->can('productions.update')) {
            abort(403);
        }

        $data = $request->validate([
            'contact_id' => 'required|exists:contacts,id',
            'primary' => 'nullable|boolean',
            'role' => 'nullable|string|max:255',
            'departments' => 'nullable|array',
            'departments.*' => 'nullable|string|max:255',
            'positions' => 'nullable|array',
            'positions.*' => 'nullable|integer|exists:positions,id',
            'notes' => 'nullable|string',
        ]);

        // Normalize pivot JSON fields
        $pivotData = [
            'role' => $data['role'] ?? null,
            'department' => null,
            'departments' => !empty($data['departments']) ? json_encode(array_values($data['departments'])) : null,
            'positions' => null,
            'notes' => $data['notes'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        if (!empty($data['positions'])) {
            // convert position ids to names for storage
            $names = \App\Models\Position::whereIn('id', $data['positions'])->pluck('name')->toArray();
            $pivotData['positions'] = json_encode(array_values($names));
        }

        $contactId = $data['contact_id'];
        $exists = $production->contacts()->where('contact_id', $contactId)->exists();
        if ($exists) {
            $production->contacts()->updateExistingPivot($contactId, $pivotData);
        } else {
            $production->contacts()->attach($contactId, $pivotData);
        }

        if (!empty($data['primary'])) {
            $production->primary_contact_id = $contactId;
            $production->save();
        }

        return redirect()->to(route('productions.show', $production) . '#contacts')->with('success', 'Contact attached.');
    }

    public function detachContact(Production $production, $contactId)
    {
        if (!auth()->user()->can('productions.update')) {
            abort(403);
        }

        $production->contacts()->detach($contactId);
        if ($production->primary_contact_id == $contactId) {
            $production->primary_contact_id = null;
            $production->save();
        }

        return redirect()->to(route('productions.show', $production) . '#contacts')->with('success', 'Contact removed.');
    }

    public function updateContactPivot(Request $request, Production $production, $contactId)
    {
        if (!auth()->user()->can('productions.update')) {
            abort(403);
        }

        $data = $request->validate([
            'role' => 'nullable|string|max:255',
            'departments' => 'nullable|array',
            'departments.*' => 'nullable|string|max:255',
            'positions' => 'nullable|array',
            'positions.*' => 'nullable|integer|exists:positions,id',
            'notes' => 'nullable|string',
        ]);

        $update = [
            'role' => $data['role'] ?? null,
            'department' => null,
            'departments' => !empty($data['departments']) ? json_encode(array_values($data['departments'])) : null,
            'positions' => null,
            'notes' => $data['notes'] ?? null,
            'updated_at' => now(),
        ];
        if (!empty($data['positions'])) {
            $names = \App\Models\Position::whereIn('id', $data['positions'])->pluck('name')->toArray();
            $update['positions'] = json_encode(array_values($names));
        }

        $production->contacts()->updateExistingPivot($contactId, $update);

        return redirect()->to(route('productions.show', $production) . '#contacts')->with('success', 'Contact updated.');
    }

    public function edit(Production $production)
    {
        $shows = Show::orderByDesc('opening_date')->get();
        $companies = Company::orderBy('name')->get();
        $contacts = Contact::orderBy('last_name')->get();
        $venues = \App\Models\Venue::orderBy('name')->get();
        $buildings = \App\Models\Building::orderBy('name')->get();
        $spaces = \App\Models\Space::with('building')->orderBy('name')->get();
        $production->load('companies','contacts','space');
        return view('productions.edit', compact('production','shows','companies','contacts','venues','buildings','spaces'));
    }

    public function update(Request $request, Production $production)
    {
        $data = $request->validate([
            'show_id' => 'required|exists:shows,id',
            'title' => 'required|string|max:255',
            'status' => 'nullable|in:New,In Production,Open,Closed,Notes,Cancelled',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'initial_contact_date' => 'nullable|date',
            'space_id' => 'nullable|exists:spaces,id',
            'notes' => 'nullable|string',
            'primary_company_id' => 'nullable|exists:companies,id',
            'primary_contact_id' => 'nullable|exists:contacts,id',
        ]);

        $production->update($data);
        if (!empty($data['primary_company_id'])) {
            $production->companies()->sync([$data['primary_company_id']]);
        } else {
            $production->companies()->sync([]);
        }
        if (!empty($data['primary_contact_id'])) {
            $production->contacts()->sync([$data['primary_contact_id']]);
        } else {
            $production->contacts()->sync([]);
        }

        return redirect()->route('productions.show', $production)->with('success','Production updated.');
    }

    public function destroy(Production $production)
    {
        $production->delete();
        return redirect()->route('productions.index')->with('success','Production deleted.');
    }
}
