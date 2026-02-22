<?php

namespace App\Http\Controllers;

use App\Models\CatalogueContact;
use App\Models\ShowCatalogue;
use Illuminate\Http\Request;

class CatalogueContactController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:show_catalogues.update');
    }

    public function store(Request $request, ShowCatalogue $showCatalogue)
    {
        $data = $request->validate([
            'contact_id' => 'required|exists:contacts,id',
            'role' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $showCatalogue->catalogueContacts()->create($data);

        return redirect()->to(route('show_catalogues.show', $showCatalogue) . '?tab=baseline-contacts')
            ->with('success', 'Contact added.');
    }

    public function update(Request $request, ShowCatalogue $showCatalogue, CatalogueContact $catalogueContact)
    {
        $data = $request->validate([
            'contact_id' => 'required|exists:contacts,id',
            'role' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $catalogueContact->update($data);

        return redirect()->to(route('show_catalogues.show', $showCatalogue) . '?tab=baseline-contacts')
            ->with('success', 'Contact updated.');
    }

    public function destroy(ShowCatalogue $showCatalogue, CatalogueContact $catalogueContact)
    {
        $catalogueContact->delete();

        return redirect()->to(route('show_catalogues.show', $showCatalogue) . '?tab=baseline-contacts')
            ->with('success', 'Contact deleted.');
    }
}
