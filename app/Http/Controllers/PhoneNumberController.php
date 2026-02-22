<?php

namespace App\Http\Controllers;

use App\Models\PhoneNumber;
use App\Models\Company;
use App\Models\Contact;
use Illuminate\Http\Request;

class PhoneNumberController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:phones.view')->only(['index','show']);
        $this->middleware('permission:phones.create')->only(['create','store']);
        $this->middleware('permission:phones.update')->only(['edit','update']);
        $this->middleware('permission:phones.delete')->only(['destroy']);
    }

    public function index()
    {
        $phones = PhoneNumber::with(['contact','company'])->paginate(30);
        return view('phones.index', compact('phones'));
    }

    public function create()
    {
        $contacts = Contact::orderBy('last_name')->get();
        $companies = Company::orderBy('name')->get();
        return view('phones.create', compact('contacts','companies'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'contact_id' => 'required|exists:contacts,id',
            'company_id' => 'nullable|exists:companies,id',
            'type' => 'nullable|string|max:100',
            'number' => 'required|string|max:50',
            'primary' => 'sometimes|boolean',
            'notes' => 'nullable|string',
        ]);

        $phone = PhoneNumber::create($data);

        if(!empty($data['primary'])){
            PhoneNumber::where('contact_id',$phone->contact_id)->where('id','!=',$phone->id)->update(['primary'=>false]);
        }

        $return = $request->input('return_to') ?? route('contacts.show', $phone->contact_id);
        return redirect($return)->with('phone_action', 'added');
    }

    public function edit(PhoneNumber $phone)
    {
        $contacts = Contact::orderBy('last_name')->get();
        $companies = Company::orderBy('name')->get();
        return view('phones.edit', compact('phone','contacts','companies'));
    }

    public function update(Request $request, PhoneNumber $phone)
    {
        $data = $request->validate([
            'contact_id' => 'required|exists:contacts,id',
            'company_id' => 'nullable|exists:companies,id',
            'type' => 'nullable|string|max:100',
            'number' => 'required|string|max:50',
            'primary' => 'sometimes|boolean',
            'notes' => 'nullable|string',
        ]);

        $phone->update($data);

        if(!empty($data['primary'])){
            PhoneNumber::where('contact_id',$phone->contact_id)->where('id','!=',$phone->id)->update(['primary'=>false]);
        }

        $return = $request->input('return_to') ?? route('contacts.show', $phone->contact_id);
        return redirect($return)->with('phone_action', 'updated');
    }

    public function destroy(Request $request, PhoneNumber $phone)
    {
        $contactId = $phone->contact_id;
        $phone->delete();
        $return = $request->input('return_to') ?? route('contacts.show', $contactId);
        return redirect($return)->with('phone_action', 'deleted');
    }
}
