@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-6">
    <h2 class="text-lg font-semibold mb-2">{{ $building->name }}</h2>

    <div class="mb-4"><strong>Venue:</strong> @if($building->venue)<a href="{{ route('venues.show', $building->venue) }}" class="text-blue-600">{{ $building->venue->name }}</a>@else<span class="text-gray-500">No venue</span>@endif</div>
    <div class="mb-4"><strong>Type:</strong> {{ $building->type }}</div>
    <div class="mb-4"><strong>Website:</strong> <a href="{{ $building->website }}">{{ $building->website }}</a></div>
    <div class="mb-4"><strong>Notes:</strong> <div class="mt-2 whitespace-pre-wrap">{{ $building->notes }}</div></div>

    <div class="mt-4">
        <a href="{{ route('buildings.index') }}">Back</a>
        @can('buildings.update')
            <a href="{{ route('buildings.edit', $building) }}" class="ml-4">Edit</a>
        @endcan
    </div>

    <div class="mt-6">
        <h3 class="text-md font-semibold">Spaces</h3>
        <ul class="mt-2">
            @forelse($building->spaces as $space)
                <li><a href="{{ route('spaces.show', $space) }}" class="text-blue-600">{{ $space->name }}</a></li>
            @empty
                <li>No spaces.</li>
            @endforelse
        </ul>
        @can('spaces.create')
            <div class="mt-4">
                <button type="button" class="btn" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'space-building-{{ $building->id }}'}))">Add Space</button>
            </div>

            <x-modal name="space-building-{{ $building->id }}" focusable>
                <div class="p-6">
                    <h4 class="text-lg font-medium mb-4">Add Space</h4>
                    <form action="{{ route('spaces.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="building_id" value="{{ $building->id }}" />
                        <input type="hidden" name="return_to" value="{{ route('buildings.show', $building) }}" />

                        <div class="grid grid-cols-1 gap-2">
                            <input name="name" placeholder="Space name" class="input" required />
                            <input name="type" placeholder="Type (conference, office, virtual)" class="input" />
                            <textarea name="notes" placeholder="Notes" class="input"></textarea>
                            <div class="flex gap-2 mt-4">
                                <button class="btn">Add</button>
                                <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'space-building-{{ $building->id }}'}))">Cancel</button>
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
                                            <input type="hidden" name="return_to" value="{{ route('buildings.show', $building) }}" />
                                            <button class="btn" type="submit">Save</button>
                                        </form>
                                    @endcan

                                    @can('employments.delete')
                                        <form action="{{ route('employments.destroy', $employment) }}" method="POST" class="inline ml-2">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="return_to" value="{{ route('buildings.show', $building) }}" />
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
                <button type="button" class="btn" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'employment-building-{{ $building->id }}'}))">Add Employment</button>
            </div>

            <x-modal name="employment-building-{{ $building->id }}" focusable>
                <div class="p-6">
                    <h4 class="text-lg font-medium mb-4">Add Employment</h4>
                    <form action="{{ route('employments.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="employable_type" value="building" />
                        <input type="hidden" name="employable_id" value="{{ $building->id }}" />
                        <input type="hidden" name="return_to" value="{{ route('buildings.show', $building) }}" />

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
                                <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'employment-building-{{ $building->id }}'}))">Cancel</button>
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
            <div class="bg-white shadow sm:rounded-lg mb-6">
                <div class="p-4">
                    @forelse($building->addresses as $address)
                        <div class="border-b py-2 flex justify-between items-start">
                            <div class="flex-1">
                                <form action="{{ route('addresses.update', $address) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="return_to" value="{{ route('buildings.show', $building) }}" />

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
                                        <input type="hidden" name="return_to" value="{{ route('buildings.show', $building) }}" />
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
        @endcan

        @can('addresses.create')
            <div class="mt-4">
                <button type="button" class="btn" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'address-building-{{ $building->id }}'}))">Add Address</button>
            </div>

            <x-modal name="address-building-{{ $building->id }}" focusable>
                <div class="p-6">
                    <h4 class="text-lg font-medium mb-4">Add Address</h4>
                    <form action="{{ route('addresses.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="addressable_type" value="building" />
                        <input type="hidden" name="addressable_id" value="{{ $building->id }}" />
                        <input type="hidden" name="return_to" value="{{ route('buildings.show', $building) }}" />

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
                                <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'address-building-{{ $building->id }}'}))">Cancel</button>
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
