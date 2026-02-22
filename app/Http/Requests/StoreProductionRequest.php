<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('productions.create');
    }

    public function rules(): array
    {
        return [
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
        ];
    }
}
