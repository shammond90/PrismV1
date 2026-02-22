<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:addresses.create')->only(['store']);
        $this->middleware('permission:addresses.update')->only(['update']);
        $this->middleware('permission:addresses.delete')->only(['destroy']);
        $this->middleware('permission:addresses.view')->only(['index', 'show']);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'addressable_type' => 'required|string|in:company,venue,building,space',
            'addressable_id' => 'required|integer',
            'type' => 'nullable|string|max:100',
            'address1' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'primary' => 'nullable|boolean',
        ]);

        $map = [
            'company' => \App\Models\Company::class,
            'venue' => \App\Models\Venue::class,
            'building' => \App\Models\Building::class,
            'space' => \App\Models\Space::class,
        ];

        $class = $map[$data['addressable_type']];
        if (!$class::find($data['addressable_id'])) {
            return back()->withErrors(['addressable_id' => 'Target not found'])->withInput();
        }

        if (!empty($data['primary'])) {
            // unset other primary addresses for this addressable
            Address::where('addressable_type', $class)
                ->where('addressable_id', $data['addressable_id'])
                ->update(['primary' => false]);
        }

        Address::create([
            'addressable_type' => $class,
            'addressable_id' => $data['addressable_id'],
            'type' => $data['type'] ?? null,
            'address1' => $data['address1'] ?? null,
            'city' => $data['city'] ?? null,
            'state' => $data['state'] ?? null,
            'country' => $data['country'] ?? null,
            'notes' => $data['notes'] ?? null,
            'primary' => !empty($data['primary']) ? 1 : 0,
        ]);

        $return = $request->input('return_to') ?? url()->previous();
        return redirect($return)->with('success', 'Address added.');
    }

    public function update(Request $request, Address $address)
    {
        $data = $request->validate([
            'type' => 'nullable|string|max:100',
            'address1' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'primary' => 'nullable|boolean',
        ]);

        if (!empty($data['primary'])) {
            Address::where('addressable_type', $address->addressable_type)
                ->where('addressable_id', $address->addressable_id)
                ->update(['primary' => false]);
            $address->primary = 1;
        } else {
            $address->primary = 0;
        }

        $address->update([
            'type' => $data['type'] ?? null,
            'address1' => $data['address1'] ?? null,
            'city' => $data['city'] ?? null,
            'state' => $data['state'] ?? null,
            'country' => $data['country'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        $address->save();

        $return = $request->input('return_to') ?? url()->previous();
        return redirect($return)->with('success', 'Address updated.');
    }

    public function destroy(Request $request, Address $address)
    {
        $return = $request->input('return_to') ?? url()->previous();
        $address->delete();
        return redirect($return)->with('success', 'Address deleted.');
    }
}
