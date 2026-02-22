@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-6">
    <h2 class="text-lg font-semibold mb-2">{{ $company->name }}</h2>

    <div class="mb-4">
        <strong>Industry:</strong> {{ $company->industry }}
    </div>
    
    {{-- Addresses --}}
    <div class="mt-8">
        <h3 class="text-md font-semibold mb-2">Addresses</h3>

        @if(session('success'))
            <div class="mb-4 text-green-600">{{ session('success') }}</div>
        @endif

        <div class="bg-white shadow sm:rounded-lg mb-6">
            <div class="p-4">
                @forelse($company->addresses as $address)
                    <div class="border-b py-2 flex justify-between items-start">
                        <div class="flex-1">
                            <form action="{{ route('addresses.update', $address) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="return_to" value="{{ route('companies.show', $company) }}" />

                                @can('addresses.update')
                                    <div class="grid grid-cols-1 gap-2">
                                        <input name="type" class="input" placeholder="Type (e.g., Billing)" value="{{ $address->type }}" />
                                        <input name="address1" class="input" placeholder="Address" value="{{ $address->address1 }}" />
                                        <div class="flex gap-2">
                                            <input name="city" class="input flex-1" placeholder="City" value="{{ $address->city }}" />
                                            <input name="state" class="input flex-1" placeholder="State" value="{{ $address->state }}" />
                                            <input name="country" class="input flex-1" placeholder="Country" value="{{ $address->country }}" />
                                        </div>
                                        <textarea name="notes" class="input" placeholder="Notes">{{ $address->notes }}</textarea>
                                        <label class="flex items-center gap-2">
                                            <input type="hidden" name="primary" value="0" />
                                            <input type="checkbox" name="primary" value="1" {{ $address->primary ? 'checked' : '' }} />
                                            <span class="text-sm text-gray-700">Primary</span>
                                        </label>
                                    </div>
                                @else
                                    <div>
                                        <div class="font-medium">{{ $address->type ? $address->type . ' - ' : '' }}{{ $address->address1 }}</div>
                                        <div class="text-sm text-gray-600">{{ $address->city }} {{ $address->state }} {{ $address->country }}</div>
                                        @if($address->notes)
                                            <div class="text-sm text-gray-700 mt-1">{{ $address->notes }}</div>
                                        @endif
                                    </div>
                                @endcan
                                @can('addresses.update')
                                    <div class="flex gap-2 mt-4">
                                        <button class="btn" type="submit">Save</button>
                                    </div>
                                @endcan
                            </form>
                        </div>

                        <div class="ml-4 text-right">
                            @if($address->primary)
                                <div class="text-xs text-white bg-blue-600 px-2 py-1 rounded">Primary</div>
                            @endif

                            @can('addresses.delete')
                                <form action="{{ route('addresses.destroy', $address) }}" method="POST" class="inline ml-2 mt-2">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="return_to" value="{{ route('companies.show', $company) }}" />
                                    <button type="submit" class="text-red-600 text-sm">Delete</button>
                                </form>
                            @endcan
                        </div>
                    </div>

                @empty
                    <div class="px-4 py-2 text-gray-600">No addresses.</div>
                @endforelse
            </div>
        </div>

        @can('addresses.create')
            <div class="mt-4">
                <button type="button" class="btn" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'address-company-{{ $company->id }}'}))">Add Address</button>
            </div>

            <x-modal name="address-company-{{ $company->id }}" focusable>
                <div class="p-6">
                    <h4 class="text-lg font-medium mb-4">Add Address</h4>
                    <form action="{{ route('addresses.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="addressable_type" value="company" />
                        <input type="hidden" name="addressable_id" value="{{ $company->id }}" />
                        <input type="hidden" name="return_to" value="{{ route('companies.show', $company) }}" />

                        <div class="grid grid-cols-1 gap-2">
                            <input name="type" class="input" placeholder="Type (e.g., Billing)" />
                            <input name="address1" class="input" placeholder="Address" />
                            <div class="flex gap-2">
                                <input name="city" class="input flex-1" placeholder="City" />
                                <input name="state" class="input flex-1" placeholder="State" />
                                <input name="country" class="input flex-1" placeholder="Country" />
                            </div>
                            <textarea name="notes" class="input" placeholder="Notes"></textarea>
                            <label class="flex items-center gap-2">
                                <input type="hidden" name="primary" value="0" />
                                <input type="checkbox" name="primary" value="1" />
                                <span class="text-sm text-gray-700">Primary</span>
                            </label>
                            <div class="flex gap-2 mt-4">
                                <button class="btn">Add</button>
                                <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'address-company-{{ $company->id }}'}))">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </x-modal>
        @endcan
    </div>
    <div class="mb-4">
        <strong>Website:</strong> <a href="{{ $company->website }}">{{ $company->website }}</a>
    </div>
    <div class="mb-4">
        <strong>Notes:</strong>
        <div class="mt-2 whitespace-pre-wrap">{{ $company->notes }}</div>
    </div>

    <div class="mt-4">
        <a href="{{ route('companies.index') }}">Back</a>
    </div>
    
    <div class="mt-8">
        <h3 class="text-md font-semibold mb-2">Employments</h3>

        @if(session('success'))
            <div class="mb-4 text-green-600">{{ session('success') }}</div>
        @endif

        @can('employments.view')
            <div class="bg-white shadow sm:rounded-lg mb-6">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($employments as $employment)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium">{{ $employment->contact->first_name }} {{ $employment->contact->last_name }}</div>
                                    <div class="text-sm text-gray-500 mt-1">
                                        @php
                                            $primaryPhone = $employment->contact->phones->firstWhere('primary', 1);
                                            $companyPhones = $employment->contact->phones->filter(function($p) use ($company, $primaryPhone) {
                                                return $p->company_id == $company->id && (!$primaryPhone || $p->id !== $primaryPhone->id);
                                            });
                                        @endphp

                                        @if($primaryPhone)
                                            <div>Primary: {{ $primaryPhone->number }}</div>
                                        @endif

                                        @foreach($companyPhones as $p)
                                            <div>{{ $p->type ? $p->type.': ' : '' }}{{ $p->number }}</div>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @can('employments.update')
                                        <form action="{{ route('employments.update', $employment) }}" method="POST" class="flex items-center">
                                            @csrf
                                            @method('PUT')
                                            <input name="position" value="{{ $employment->position }}" class="input" />
                                    @else
                                        {{ $employment->position }}
                                    @endcan
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @can('employments.update')
                                            <input name="department" value="{{ $employment->department }}" class="input ml-2" />
                                    @else
                                        {{ $employment->department }}
                                    @endcan
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @can('employments.update')
                                            <input type="date" name="start_date" value="{{ $employment->start_date }}" class="input" />
                                    @else
                                        {{ $employment->start_date }}
                                    @endcan
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @can('employments.update')
                                            <input type="date" name="end_date" value="{{ $employment->end_date }}" class="input" />
                                    @else
                                        {{ $employment->end_date }}
                                    @endcan
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    @can('employments.update')
                                            <input type="hidden" name="return_to" value="{{ route('companies.show', $company) }}" />
                                            <button class="btn" type="submit">Save</button>
                                        </form>
                                    @endcan

                                    @can('employments.delete')
                                        <form action="{{ route('employments.destroy', $employment) }}" method="POST" class="inline ml-2">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="return_to" value="{{ route('companies.show', $company) }}" />
                                            <button type="submit" class="text-red-600">Delete</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-4">No employment records.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endcan

        @can('employments.create')
            <div class="mt-4">
                <button type="button" class="btn" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'employment-company-{{ $company->id }}'}))">Add Employment</button>
            </div>

            <x-modal name="employment-company-{{ $company->id }}" focusable>
                <div class="p-6">
                    <h4 class="text-lg font-medium mb-4">Add Employment</h4>
                    <form action="{{ route('employments.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="employable_type" value="company" />
                        <input type="hidden" name="employable_id" value="{{ $company->id }}" />
                        <input type="hidden" name="return_to" value="{{ route('companies.show', $company) }}" />

                        <div class="grid grid-cols-1 gap-2">
                            <select name="contact_id" class="input" required>
                                <option value="">Select contact</option>
                                @foreach($contacts as $contact)
                                    <option value="{{ $contact->id }}">{{ $contact->first_name }} {{ $contact->last_name }}</option>
                                @endforeach
                            </select>
                            <input name="position" placeholder="Position" class="input" />
                            <input name="department" placeholder="Department" class="input" />
                            <div class="flex gap-2">
                                <input type="date" name="start_date" class="input" />
                                <input type="date" name="end_date" class="input" />
                            </div>
                            <div class="flex gap-2 mt-4">
                                <button class="btn">Add</button>
                                <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'employment-company-{{ $company->id }}'}))">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </x-modal>
        @endcan
    </div>
</div>
@endsection
