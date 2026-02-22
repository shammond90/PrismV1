@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-6">
    <h2 class="text-lg font-semibold mb-2">{{ $venue->name }}</h2>

    <div class="mb-4"><strong>Type:</strong> {{ $venue->type }}</div>
    <div class="mb-4"><strong>Website:</strong> <a href="{{ $venue->website }}">{{ $venue->website }}</a></div>
    <div class="mb-4"><strong>Notes:</strong> <div class="mt-2 whitespace-pre-wrap">{{ $venue->notes }}</div></div>

    <div class="mt-4">
        <a href="{{ route('venues.index') }}">Back</a>
        @can('venues.update')
            <a href="{{ route('venues.edit', $venue) }}" class="ml-4">Edit</a>
        @endcan
    </div>

    <div class="mt-6">
        <h3 class="text-md font-semibold">Buildings</h3>
        <ul class="mt-2">
            @forelse($venue->buildings as $building)
                <li><a href="{{ route('buildings.show', $building) }}" class="text-blue-600">{{ $building->name }}</a></li>
            @empty
                <li>No buildings.</li>
            @endforelse
        </ul>
        @can('buildings.create')
            <div class="mt-4">
                <button type="button" class="btn" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'building-venue-{{ $venue->id }}'}))">Add Building</button>
            </div>

            <x-modal name="building-venue-{{ $venue->id }}" focusable>
                <div class="p-6">
                    <h4 class="text-lg font-medium mb-4">Add Building</h4>
                    <form action="{{ route('buildings.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="venue_id" value="{{ $venue->id }}" />
                        <input type="hidden" name="return_to" value="{{ route('venues.show', $venue) }}" />

                        <div class="grid grid-cols-1 gap-2">
                            <input name="name" placeholder="Building name" class="input" required />
                            <input name="type" placeholder="Type (e.g., office)" class="input" />
                            <input name="website" placeholder="Website" class="input" />
                            <textarea name="notes" placeholder="Notes" class="input"></textarea>
                            <div class="flex gap-2 mt-4">
                                <button class="btn">Add</button>
                                <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'building-venue-{{ $venue->id }}'}))">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </x-modal>
        @endcan
    </div>

    {{-- Employments Section --}}
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
                                    <a href="{{ route('contacts.show', $employment->contact) }}" class="font-medium text-blue-600">{{ $employment->contact->first_name }} {{ $employment->contact->last_name }}</a>
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
                                            <input type="hidden" name="return_to" value="{{ route('venues.show', $venue) }}" />
                                            <button class="btn" type="submit">Save</button>
                                        </form>
                                    @endcan

                                    @can('employments.delete')
                                        <form action="{{ route('employments.destroy', $employment) }}" method="POST" class="inline ml-2">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="return_to" value="{{ route('venues.show', $venue) }}" />
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
                <button type="button" class="btn" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'employment-venue-{{ $venue->id }}'}))">Add Employment</button>
            </div>

            <x-modal name="employment-venue-{{ $venue->id }}" focusable>
                <div class="p-6">
                    <h4 class="text-lg font-medium mb-4">Add Employment</h4>
                    <form action="{{ route('employments.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="employable_type" value="venue" />
                        <input type="hidden" name="employable_id" value="{{ $venue->id }}" />
                        <input type="hidden" name="return_to" value="{{ route('venues.show', $venue) }}" />

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
                                <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'employment-venue-{{ $venue->id }}'}))">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </x-modal>
        @endcan
    
    {{-- Addresses --}}
    <div class="mt-8">
        <h3 class="text-md font-semibold mb-2">Addresses</h3>

        @if(session('success'))
            <div class="mb-4 text-green-600">{{ session('success') }}</div>
        @endif

        @can('addresses.view')
            <div class="mt-4">
                <button type="button" class="btn" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'address-venue-{{ $venue->id }}'}))">Add Address</button>
            </div>
            <x-modal name="address-venue-{{ $venue->id }}" focusable>
                <div class="p-6">
                    <h4 class="text-lg font-medium mb-4">Add Address</h4>
                    <form action="{{ route('addresses.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="addressable_type" value="venue" />
                        <input type="hidden" name="addressable_id" value="{{ $venue->id }}" />
                        <input type="hidden" name="return_to" value="{{ route('venues.show', $venue) }}" />

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
                                <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'address-venue-{{ $venue->id }}'}))">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </x-modal>
        @endcan
    </div>
    </div>
</div>
@endsection
