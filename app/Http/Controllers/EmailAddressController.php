<?php

namespace App\Http\Controllers;

use App\Models\EmailAddress;
use App\Models\Company;
use App\Models\Contact;
use Illuminate\Http\Request;

class EmailAddressController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:emails.view')->only(['index','show']);
        $this->middleware('permission:emails.create')->only(['create','store']);
        $this->middleware('permission:emails.update')->only(['edit','update']);
        $this->middleware('permission:emails.delete')->only(['destroy']);
    }

    public function index()
    {
        $emails = EmailAddress::with(['contact','company'])->paginate(30);
        return view('emails.index', compact('emails'));
    }

    public function create()
    {
        $contacts = Contact::orderBy('last_name')->get();
        $companies = Company::orderBy('name')->get();
        return view('emails.create', compact('contacts','companies'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'contact_id' => 'required|exists:contacts,id',
            'company_id' => 'nullable|exists:companies,id',
            'type' => 'nullable|string|max:100',
            'address' => 'required|email|max:255',
            'primary' => 'sometimes|boolean',
            'notes' => 'nullable|string',
        ]);

        $email = EmailAddress::create($data);

        if(!empty($data['primary'])){
            EmailAddress::where('contact_id',$email->contact_id)->where('id','!=',$email->id)->update(['primary'=>false]);
        }

        $return = $request->input('return_to') ?? route('contacts.show', $email->contact_id);
        return redirect($return)->with('email_action', 'added');
    }

    public function edit(EmailAddress $email)
    {
        $contacts = Contact::orderBy('last_name')->get();
        $companies = Company::orderBy('name')->get();
        return view('emails.edit', compact('email','contacts','companies'));
    }

    public function update(Request $request, EmailAddress $email)
    {
        $data = $request->validate([
            'contact_id' => 'required|exists:contacts,id',
            'company_id' => 'nullable|exists:companies,id',
            'type' => 'nullable|string|max:100',
            'address' => 'required|email|max:255',
            'primary' => 'sometimes|boolean',
            'notes' => 'nullable|string',
        ]);

        $email->update($data);

        if(!empty($data['primary'])){
            EmailAddress::where('contact_id',$email->contact_id)->where('id','!=',$email->id)->update(['primary'=>false]);
        }

        $return = $request->input('return_to') ?? route('contacts.show', $email->contact_id);
        return redirect($return)->with('email_action', 'updated');
    }

    public function destroy(Request $request, EmailAddress $email)
    {
        $contactId = $email->contact_id;
        $email->delete();
        $return = $request->input('return_to') ?? route('contacts.show', $contactId);
        return redirect($return)->with('email_action', 'deleted');
    }
}
