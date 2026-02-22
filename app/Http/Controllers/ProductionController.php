<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductionRequest;
use App\Http\Requests\UpdateProductionRequest;
use App\Models\Building;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Department;
use App\Models\EventType;
use App\Models\Position;
use App\Models\Production;
use App\Models\Show;
use App\Models\Space;
use App\Models\Venue;
use Illuminate\Http\Request;

class ProductionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:productions.view')->only(['index', 'show']);
        $this->middleware('permission:productions.create')->only(['create', 'store']);
        $this->middleware('permission:productions.update')->only([
            'edit', 'update',
            'attachCompany', 'detachCompany',
            'attachContact', 'detachContact', 'updateContactPivot',
        ]);
        $this->middleware('permission:productions.delete')->only(['destroy']);
    }

    // ──────────────────────────────────────────
    //  Standard CRUD
    // ──────────────────────────────────────────

    public function index()
    {
        $productions = Production::with('show')->orderByDesc('start_date')->get();

        return view('productions.index', compact('productions'));
    }

    public function create()
    {
        return view('productions.create', $this->formDropdowns());
    }

    public function store(StoreProductionRequest $request)
    {
        $data = $request->validated();

        // Newly created productions always start with status 'New'
        $data['status'] = 'New';

        $production = Production::create($data);

        // Seed the pivot tables so the primary selections are also visible
        // in the many-to-many relationship lists.
        if (!empty($data['primary_company_id'])) {
            $production->companies()->syncWithoutDetaching([$data['primary_company_id']]);
        }
        if (!empty($data['primary_contact_id'])) {
            $production->contacts()->syncWithoutDetaching([$data['primary_contact_id']]);
        }

        return redirect()->route('productions.show', $production)
            ->with('success', 'Production created.');
    }

    public function show(Production $production)
    {
        $production->load(
            'companies',
            'contacts.emails',
            'contacts.phones',
            'show',
            'space',
            'primaryContact.emails',
            'primaryContact.phones',
            'primaryCompany'
        );

        $events = $production->events()->with('space')->orderBy('start_at')->get();
        $departments = Department::with('positions')->orderBy('name')->get();
        $eventTypes = EventType::orderBy('name')->get();

        return view('productions.show', array_merge(
            compact('production', 'events', 'departments', 'eventTypes'),
            $this->formDropdowns()
        ));
    }

    public function edit(Production $production)
    {
        $production->load('companies', 'contacts', 'space');

        return view('productions.edit', array_merge(
            compact('production'),
            $this->formDropdowns()
        ));
    }

    public function update(UpdateProductionRequest $request, Production $production)
    {
        $data = $request->validated();

        $production->update($data);

        // Only update the FK columns — pivot rows are managed by
        // the dedicated attach/detach endpoints, so we must NOT call
        // sync() here (which would wipe rows added via those endpoints).
        $production->primary_company_id = $data['primary_company_id'] ?? null;
        $production->primary_contact_id = $data['primary_contact_id'] ?? null;
        $production->save();

        return redirect()->route('productions.show', $production)
            ->with('success', 'Production updated.');
    }

    public function destroy(Production $production)
    {
        $production->delete();

        return redirect()->route('productions.index')
            ->with('success', 'Production deleted.');
    }

    // ──────────────────────────────────────────
    //  Company pivot
    // ──────────────────────────────────────────

    public function attachCompany(Request $request, Production $production)
    {
        $data = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'primary'    => 'nullable|boolean',
        ]);

        $production->companies()->syncWithoutDetaching([$data['company_id']]);

        if (!empty($data['primary'])) {
            $production->update(['primary_company_id' => $data['company_id']]);
        }

        return redirect()->to(route('productions.show', $production) . '#companies')
            ->with('success', 'Company attached.');
    }

    public function detachCompany(Production $production, $companyId)
    {
        $production->companies()->detach($companyId);

        if ($production->primary_company_id == $companyId) {
            $production->update(['primary_company_id' => null]);
        }

        return redirect()->to(route('productions.show', $production) . '#companies')
            ->with('success', 'Company removed.');
    }

    // ──────────────────────────────────────────
    //  Contact pivot
    // ──────────────────────────────────────────

    public function attachContact(Request $request, Production $production)
    {
        $data = $request->validate([
            'contact_id'    => 'required|exists:contacts,id',
            'primary'       => 'nullable|boolean',
            'role'          => 'nullable|string|max:255',
            'departments'   => 'nullable|array',
            'departments.*' => 'nullable|string|max:255',
            'positions'     => 'nullable|array',
            'positions.*'   => 'nullable|integer|exists:positions,id',
            'notes'         => 'nullable|string',
        ]);

        $pivotData = $this->buildContactPivotData($data);
        $pivotData['created_at'] = now();
        $pivotData['updated_at'] = now();

        $contactId = $data['contact_id'];

        if ($production->contacts()->where('contact_id', $contactId)->exists()) {
            $production->contacts()->updateExistingPivot($contactId, $pivotData);
        } else {
            $production->contacts()->attach($contactId, $pivotData);
        }

        if (!empty($data['primary'])) {
            $production->update(['primary_contact_id' => $contactId]);
        }

        return redirect()->to(route('productions.show', $production) . '#contacts')
            ->with('success', 'Contact attached.');
    }

    public function detachContact(Production $production, $contactId)
    {
        $production->contacts()->detach($contactId);

        if ($production->primary_contact_id == $contactId) {
            $production->update(['primary_contact_id' => null]);
        }

        return redirect()->to(route('productions.show', $production) . '#contacts')
            ->with('success', 'Contact removed.');
    }

    public function updateContactPivot(Request $request, Production $production, $contactId)
    {
        $data = $request->validate([
            'role'          => 'nullable|string|max:255',
            'departments'   => 'nullable|array',
            'departments.*' => 'nullable|string|max:255',
            'positions'     => 'nullable|array',
            'positions.*'   => 'nullable|integer|exists:positions,id',
            'notes'         => 'nullable|string',
        ]);

        $pivotData = $this->buildContactPivotData($data);
        $pivotData['updated_at'] = now();

        $production->contacts()->updateExistingPivot($contactId, $pivotData);

        return redirect()->to(route('productions.show', $production) . '#contacts')
            ->with('success', 'Contact updated.');
    }

    // ──────────────────────────────────────────
    //  Private helpers
    // ──────────────────────────────────────────

    /**
     * Common dropdown data used by create / edit / show views.
     */
    private function formDropdowns(): array
    {
        return [
            'shows'      => Show::orderByDesc('opening_date')->get(),
            'companies'  => Company::orderBy('name')->get(),
            'contacts'   => Contact::with('emails', 'phones')->orderBy('last_name')->get(),
            'venues'     => Venue::orderBy('name')->get(),
            'buildings'  => Building::orderBy('name')->get(),
            'spaces'     => Space::with('building')->orderBy('name')->get(),
        ];
    }

    /**
     * Normalize validated contact-pivot data into the array stored
     * on the contact_production pivot table.
     */
    private function buildContactPivotData(array $data): array
    {
        $pivot = [
            'role'        => $data['role'] ?? null,
            'department'  => null,
            'departments' => !empty($data['departments'])
                ? json_encode(array_values($data['departments']))
                : null,
            'positions'   => null,
            'notes'       => $data['notes'] ?? null,
        ];

        if (!empty($data['positions'])) {
            $names = Position::whereIn('id', $data['positions'])
                ->pluck('name')
                ->toArray();
            $pivot['positions'] = json_encode(array_values($names));
        }

        return $pivot;
    }
}
