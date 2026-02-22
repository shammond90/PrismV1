@extends('layouts.app')

@php
use Illuminate\Support\Str;
use Carbon\Carbon;
@endphp

@section('content')
<div class="max-w-3xl mx-auto py-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="font-semibold text-xl text-gray-800">{{ $contact->full_name ?? ($contact->first_name.' '.$contact->last_name) }}</h2>
        <div>
            @can('contacts.update')
                <a href="{{ route('contacts.edit', $contact) }}" class="text-blue-600">Edit</a>
            @endcan
            <a href="{{ route('contacts.index') }}" class="ml-4 text-gray-600">Back</a>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">

    <div class="mb-4 border-b">
        <nav class="-mb-px flex space-x-6" aria-label="Tabs">
            <button data-tab="overview" class="tab-btn pb-2 text-sm font-medium text-gray-700 border-b-2 border-transparent">Overview</button>
            <button data-tab="contact-info" class="tab-btn pb-2 text-sm font-medium text-gray-700 border-b-2 border-transparent">Contact Info</button>
            <button data-tab="employment-info" class="tab-btn pb-2 text-sm font-medium text-gray-700 border-b-2 border-transparent">Employment Info</button>
            <button data-tab="notes" class="tab-btn pb-2 text-sm font-medium text-gray-700 border-b-2 border-transparent">Notes</button>
            <button data-tab="files" class="tab-btn pb-2 text-sm font-medium text-gray-700 border-b-2 border-transparent">Files</button>
        </nav>
    </div>

    @php
        $primaryPhone = isset($phones) ? $phones->firstWhere('primary', 1) : ($contact->phones->firstWhere('primary', 1) ?? null);
        $primaryEmail = isset($emails) ? $emails->firstWhere('primary', 1) : ($contact->emails->firstWhere('primary', 1) ?? null);
        $activeEmployments = isset($employments) ? $employments->filter(function($e){ return empty($e->end_date); })->values() : ($contact->employments->whereNull('end_date') ?? collect());

        $productions = $contact->relationLoaded('productions') ? $contact->productions : $contact->productions()->with('show')->get();
        $now = Carbon::now();
        $futureProductions = $productions->filter(function($p) use ($now){ return $p->end_date && $p->end_date->gt($now); })->values();
        $pastProductions = $productions->filter(function($p) use ($now){ return $p->end_date && $p->end_date->lte($now); })->sortByDesc(function($p){ return $p->end_date; })->values();
        $recentProductions = $futureProductions->merge($pastProductions->take(3))->sortByDesc(function($p){ return $p->end_date; })->values();
        $allProductions = $productions->sortByDesc('end_date')->values();
    @endphp

    <div id="tab-overview" class="tab-panel">
        <div class="mb-4">
            <strong>Title:</strong> {{ $contact->title }}
        </div>
        <div class="mb-4">
            <strong>Pronouns:</strong> {{ $contact->pronouns }}
        </div>
        <div class="mb-4">
            <strong>Locations:</strong> {{ implode(', ', $contact->locations ?? []) }}
        </div>

        
        <div class="mb-4">
            <strong>Primary Phone:</strong>
            @if($primaryPhone)
                <a href="tel:{{ $primaryPhone->number }}" class="text-blue-600">{{ $primaryPhone->number }}</a>
            @else
                —
            @endif
        </div>
        <div class="mb-4">
            <strong>Primary Email:</strong>
            @if($primaryEmail)
                <a href="mailto:{{ $primaryEmail->address }}" class="text-blue-600">{{ $primaryEmail->address }}</a>
            @else
                —
            @endif
        </div>

        <div class="mb-4">
            <strong>Active Employments:</strong>
            @if($activeEmployments && $activeEmployments->count())
                <ul class="mt-2 ml-4 list-disc">
                    @foreach($activeEmployments as $ae)
                        <li>
                            {{ $ae->position ?? 'Position' }} @if($ae->employable)
                                @php $short = strtolower(class_basename($ae->employable_type)); $routeName = Str::plural($short) . '.show'; @endphp
                                at <a href="{{ route($routeName, $ae->employable) }}" class="text-blue-600">{{ $ae->employable->name ?? 'Employer' }}</a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @else
                —
            @endif
        </div>

        <div class="mb-4">
            <strong>Recent Shows:</strong>
            {{-- recentProductions computed above: future productions + last 3 past --}}

            @if($recentProductions->count())
                <div class="mt-2 bg-white shadow sm:rounded-lg">
                    <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Show</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Date</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Positions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($recentProductions as $p)
                                    @php
                                        $show = $p->show ?? null;
                                        $dept = [];
                                        $pos = [];
                                        if (!empty($p->pivot->departments)) {
                                            try { $dept = is_array($p->pivot->departments) ? $p->pivot->departments : json_decode($p->pivot->departments, true) ?? []; } catch (\Exception $e) { $dept = []; }
                                        } elseif (!empty($p->pivot->department)) {
                                            $dept = [$p->pivot->department];
                                        }
                                        if (!empty($p->pivot->positions)) {
                                            try { $pos = is_array($p->pivot->positions) ? $p->pivot->positions : json_decode($p->pivot->positions, true) ?? []; } catch (\Exception $e) { $pos = []; }
                                        }
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            @if($show)
                                                <a href="{{ route('shows.show', $show) }}" class="text-blue-600">{{ $show->title }}</a>
                                            @else
                                                <span class="text-gray-700">{{ $p->title ?? 'Production #' . $p->id }}</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ optional($p->end_date)->toDateString() ?? '—' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $dept ? implode(', ', $dept) : '—' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $pos ? implode(', ', $pos) : '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                —
            @endif
        </div>

        

        <div class="mt-4">
            <a href="{{ route('contacts.index') }}">Back</a>
        </div>
    </div>

    <div id="tab-employment-info" class="tab-panel hidden">
        <div class="mt-8">
            <div class="flex items-center justify-between">
                <h3 class="text-md font-semibold mb-2">Employments</h3>
                @can('employments.create')
                    <button type="button" class="btn" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'employment-contact-{{ $contact->id }}'}))">Add Employment</button>
                @endcan
            </div>

        @if(session('employment_action'))
            @php
                $employmentMessage = null;
                if(session('employment_action') === 'added') { $employmentMessage = 'A new Employment has been added.'; }
                elseif(session('employment_action') === 'updated') { $employmentMessage = 'An Employment has been updated.'; }
                elseif(session('employment_action') === 'deleted') { $employmentMessage = 'An Employment has been deleted.'; }
            @endphp
            @if($employmentMessage)
                <div class="mb-4 text-green-600">{{ $employmentMessage }}</div>
            @endif
        @endif

        @can('employments.view')
            <div class="bg-white shadow sm:rounded-lg mb-6">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($employments as $employment)
                            @php
                                $emp = $employment->employable;
                                $shortType = $employment->employable_type ? strtolower(class_basename($employment->employable_type)) : 'company';
                                $empRouteName = str($shortType)->plural() . '.show';
                            @endphp
                            <tr>
                                <td class="px-6 py-4 align-top">
                                    <button type="button" class="text-left w-full" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'view-employment-{{ $employment->id }}'}))">
                                        <div class="font-medium">{{ $employment->position ?? 'Position' }}</div>
                                    </button>
                                </td>
                                <td class="px-6 py-4 align-top">
                                    @if($emp)
                                        <a href="{{ route($empRouteName, $emp) }}" class="text-blue-600">{{ $emp->name ?? 'Employer' }}</a>
                                    @else
                                        <span class="text-gray-600"></span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 align-top"><div class="text-sm text-gray-600">{{ $employment->start_date ?? '' }}</div></td>
                                <td class="px-6 py-4 align-top"><div class="text-sm text-gray-600">{{ $employment->end_date ?? '' }}</div></td>
                                <td class="px-6 py-4 text-right text-sm">
                                    @can('employments.update')
                                        <button type="button" class="text-blue-600 mr-3" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'edit-employment-{{ $employment->id }}'}))">Edit</button>
                                    @endcan
                                    @can('employments.delete')
                                        <button type="button" class="text-red-600" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'delete-employment-{{ $employment->id }}'}))">Delete</button>
                                    @endcan
                                </td>
                            </tr>

                            {{-- View Modal --}}
                            <x-modal name="view-employment-{{ $employment->id }}" focusable>
                                <div class="p-6">
                                    <h4 class="text-lg font-medium mb-4">View Employment</h4>
                                    <div class="grid grid-cols-1 gap-2 mb-4">
                                        <div><strong>Position</strong><div class="text-sm text-gray-600">{{ $employment->position }}</div></div>
                                        <div><strong>Department</strong><div class="text-sm text-gray-600">{{ $employment->department ?? '' }}</div></div>
                                        <div><strong>Employer</strong><div class="text-sm text-gray-600">{{ $emp ? $emp->name : '' }}</div></div>
                                        <div><strong>Start</strong><div class="text-sm text-gray-600">{{ $employment->start_date ?? '' }}</div></div>
                                        <div><strong>End</strong><div class="text-sm text-gray-600">{{ $employment->end_date ?? '' }}</div></div>
                                    </div>
                                    <div class="flex gap-2">
                                        @can('employments.update')
                                            <button type="button" class="btn" onclick="(function(){window.dispatchEvent(new CustomEvent('close-modal',{detail:'view-employment-{{ $employment->id }}'}));window.dispatchEvent(new CustomEvent('open-modal',{detail:'edit-employment-{{ $employment->id }}'}));})();">Edit</button>
                                        @endcan
                                        <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'view-employment-{{ $employment->id }}'}))">Close</button>
                                    </div>
                                </div>
                            </x-modal>

                            {{-- Edit Modal --}}
                            <x-modal name="edit-employment-{{ $employment->id }}" focusable>
                                <div class="p-6">
                                    <h4 class="text-lg font-medium mb-4">Edit Employment</h4>
                                    <form action="{{ route('employments.update', $employment) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="contact_id" value="{{ $contact->id }}" />
                                        <input type="hidden" name="return_to" value="{{ route('contacts.show', $contact) }}?tab=employment-info" />
                                        <div class="grid grid-cols-1 gap-2">
                                            <input name="position" value="{{ $employment->position }}" class="input" placeholder="Position" />
                                            <input name="department" value="{{ $employment->department }}" class="input" placeholder="Department" />
                                            <div class="flex gap-2">
                                                <input type="date" name="start_date" class="input flex-1" value="{{ $employment->start_date }}" />
                                                <input type="date" name="end_date" class="input flex-1" value="{{ $employment->end_date }}" />
                                            </div>
                                            <div class="flex gap-2 mt-4">
                                                <button class="btn">Save</button>
                                                <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'edit-employment-{{ $employment->id }}'}))">Cancel</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </x-modal>

                            {{-- Delete Modal --}}
                            <x-modal name="delete-employment-{{ $employment->id }}" focusable>
                                <div class="p-6">
                                    <h4 class="text-lg font-medium mb-4">Delete Employment</h4>
                                    <div class="mb-4 text-gray-700">Are you sure you want to delete {{ $employment->position }} at {{ $emp ? $emp->name : 'the employer' }}?</div>
                                    <div class="flex gap-2">
                                        <form action="{{ route('employments.destroy', $employment) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="return_to" value="{{ route('contacts.show', $contact) }}?tab=employment-info" />
                                            <button class="text-red-600">Yes</button>
                                        </form>
                                        <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'delete-employment-{{ $employment->id }}'}))">No</button>
                                    </div>
                                </div>
                            </x-modal>

                        @empty
                            <tr><td colspan="5" class="px-6 py-4">No employment records.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endcan

        @can('employments.create')
            <x-modal name="employment-contact-{{ $contact->id }}" focusable>
                <div class="p-6" x-data="employmentForm()">
                    <h4 class="text-lg font-medium mb-4">Add Employment</h4>
                    <form action="{{ route('employments.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="contact_id" value="{{ $contact->id }}" />
                        <input type="hidden" name="return_to" value="{{ route('contacts.show', $contact) }}?tab=employment-info" />

                        <div class="grid grid-cols-1 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Employer Type</label>
                                <select x-model="employerType" @change="loadEmployers()" class="input w-full">
                                    <option value="all">All Types</option>
                                    <option value="company">Company</option>
                                    <option value="venue">Venue</option>
                                    <option value="building">Building</option>
                                    <option value="space">Space</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Employer</label>
                                <input type="hidden" name="employable_type" x-bind:value="selectedType">
                                <input type="hidden" name="employable_id" x-bind:value="selectedId">
                                <select x-model="selectedEmployer" @change="updateSelection()" class="input w-full" required>
                                    <option value="">Select employer...</option>
                                    <template x-for="emp in employers" :key="emp.type + '-' + emp.id">
                                        <option :value="emp.type + '|' + emp.id" x-text="(emp.display_name ? emp.display_name : emp.name) + ' (' + emp.type_label + ')'">
                                        </option>
                                    </template>
                                </select>
                            </div>

                            <input name="position" placeholder="Position" class="input" />
                            <input name="department" placeholder="Department" class="input" />
                            <div class="flex gap-2">
                                <input type="date" name="start_date" class="input flex-1" />
                                <input type="date" name="end_date" class="input flex-1" />
                            </div>
                            <div class="flex gap-2 mt-4">
                                <button type="submit" class="btn" :disabled="!selectedId">Add</button>
                                <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'employment-contact-{{ $contact->id }}'}))">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>

                <script>
                    function employmentForm() {
                        return {
                            employerType: 'all',
                            employers: [],
                            selectedEmployer: '',
                            selectedType: '',
                            selectedId: '',
                            async loadEmployers() {
                                const url = '{{ route("employables.search") }}?type=' + this.employerType;
                                const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }});
                                const data = await res.json();
                                this.employers = data.results || [];
                                this.selectedEmployer = '';
                                this.selectedType = '';
                                this.selectedId = '';
                            },
                            updateSelection() {
                                if (this.selectedEmployer) {
                                    const parts = this.selectedEmployer.split('|');
                                    this.selectedType = parts[0];
                                    this.selectedId = parts[1];
                                } else {
                                    this.selectedType = '';
                                    this.selectedId = '';
                                }
                            },
                            init() {
                                this.loadEmployers();
                            }
                        }
                    }
                </script>
            </x-modal>
        @endcan

        <div class="mt-6">
            <h3 class="text-md font-semibold mb-2">Shows</h3>
            @if($allProductions->count())
                <div class="bg-white shadow sm:rounded-lg mb-6">
                    <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Show</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Date</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Positions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($allProductions as $p)
                                    @php
                                        $show = $p->show ?? null;
                                        $dept = [];
                                        $pos = [];
                                        if (!empty($p->pivot->departments)) {
                                            try { $dept = is_array($p->pivot->departments) ? $p->pivot->departments : json_decode($p->pivot->departments, true) ?? []; } catch (\Exception $e) { $dept = []; }
                                        } elseif (!empty($p->pivot->department)) {
                                            $dept = [$p->pivot->department];
                                        }
                                        if (!empty($p->pivot->positions)) {
                                            try { $pos = is_array($p->pivot->positions) ? $p->pivot->positions : json_decode($p->pivot->positions, true) ?? []; } catch (\Exception $e) { $pos = []; }
                                        }
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            @if($show)
                                                <a href="{{ route('shows.show', $show) }}" class="text-blue-600">{{ $show->title }}</a>
                                            @else
                                                <span class="text-gray-700">{{ $p->title ?? 'Production #' . $p->id }}</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ optional($p->end_date)->toDateString() ?? '—' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $dept ? implode(', ', $dept) : '—' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $pos ? implode(', ', $pos) : '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="text-gray-600">No shows associated with this contact.</div>
            @endif
        </div>

        </div>
    </div>

    <div id="tab-contact-info" class="tab-panel hidden">
        <div class="mt-8">
            <div class="flex items-center justify-between">
                <h3 class="text-md font-semibold mb-2">Phone Numbers</h3>
                @can('phones.create')
                    <button type="button" class="btn" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'phone-contact-{{ $contact->id }}'}))">Add Phone</button>
                @endcan
            </div>

            @php
                $phoneMessage = null;
                if(session('phone_action') === 'added') { $phoneMessage = 'A new Phone Number has been Added.'; }
                elseif(session('phone_action') === 'updated') { $phoneMessage = 'A Phone Number has been Updated.'; }
                elseif(session('phone_action') === 'deleted') { $phoneMessage = 'A phone Number has been Deleted.'; }
            @endphp
            @if($phoneMessage)
                <div class="mb-4 text-green-600">{{ $phoneMessage }}</div>
            @endif

        @can('phones.view')
            @php
                $sortedPhones = isset($phones) ? $phones->sortByDesc('primary')->values() : ($contact->phones->sortByDesc('primary')->values() ?? collect());
            @endphp
            <div class="bg-white shadow sm:rounded-lg mb-6">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone Number</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($sortedPhones as $phone)
                            <tr>
                                <td class="px-6 py-4 align-top">
                                    <button type="button" class="text-left w-full" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'view-phone-{{ $phone->id }}'}))">
                                        <div class="font-medium">{{ $phone->type ?? 'Phone' }} @if($phone->primary) <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">Primary</span>@endif</div>
                                        @if($phone->company)
                                            <div class="text-sm text-gray-600">{{ $phone->company->name }}</div>
                                        @endif
                                    </button>
                                </td>
                                <td class="px-6 py-4 align-top"><a href="tel:{{ $phone->number }}" class="text-blue-600">{{ $phone->number }}</a></td>
                                <td class="px-6 py-4 text-right text-sm">
                                    @can('phones.update')
                                        <button type="button" class="text-blue-600 mr-3" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'edit-phone-{{ $phone->id }}'}))">Edit</button>
                                    @endcan
                                    @can('phones.delete')
                                        <button type="button" class="text-red-600" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'delete-phone-{{ $phone->id }}'}))">Delete</button>
                                    @endcan
                                </td>
                            </tr>

                            {{-- View Modal --}}
                            <x-modal name="view-phone-{{ $phone->id }}" focusable>
                                <div class="p-6">
                                    <h4 class="text-lg font-medium mb-4">View Phone</h4>
                                    <div class="grid grid-cols-1 gap-2 mb-4">
                                        <div><strong>Type</strong><div class="text-gray-700">{{ $phone->type }}</div></div>
                                        @if($phone->company)
                                            <div><strong>Company</strong><div class="text-gray-700">{{ $phone->company->name }}</div></div>
                                        @endif
                                        <div><strong>Phone Number</strong><div class="text-gray-700">{{ $phone->number }}</div></div>
                                        <div><strong>Notes</strong><div class="text-gray-700">{{ $phone->notes ?? '—' }}</div></div>
                                    </div>
                                    <div class="flex gap-2">
                                        @can('phones.update')
                                            <button type="button" class="btn" onclick="(function(){window.dispatchEvent(new CustomEvent('close-modal',{detail:'view-phone-{{ $phone->id }}'}));window.dispatchEvent(new CustomEvent('open-modal',{detail:'edit-phone-{{ $phone->id }}'}));})();">Edit</button>
                                        @endcan
                                        <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'view-phone-{{ $phone->id }}'}))">Close</button>
                                    </div>
                                </div>
                            </x-modal>

                            {{-- Edit Modal --}}
                            <x-modal name="edit-phone-{{ $phone->id }}" focusable>
                                <div class="p-6">
                                    <h4 class="text-lg font-medium mb-4">Edit Phone</h4>
                                    <form action="{{ route('phones.update', $phone) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="contact_id" value="{{ $contact->id }}" />
                                        <input type="hidden" name="return_to" value="{{ route('contacts.show', $contact) }}?tab=contact-info" />

                                        <div class="grid grid-cols-1 gap-2">
                                            <input name="type" value="{{ $phone->type }}" class="input" placeholder="Type (e.g. Mobile)" />
                                            <select name="company_id" class="input">
                                                <option value="">Associate with company (optional)</option>
                                                @foreach($companies as $company)
                                                    <option value="{{ $company->id }}" {{ $phone->company_id == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                                                @endforeach
                                            </select>
                                            <input name="number" value="{{ $phone->number }}" placeholder="(555) 555-5555" class="input" required />
                                            <label class="flex items-center gap-2">
                                                <input type="hidden" name="primary" value="0" />
                                                <input type="checkbox" name="primary" value="1" {{ $phone->primary ? 'checked' : '' }} />
                                                <span class="text-sm text-gray-700">Primary</span>
                                            </label>
                                            <textarea name="notes" class="input" placeholder="Notes">{{ $phone->notes }}</textarea>
                                            <div class="flex gap-2 mt-4">
                                                <button class="btn">Save</button>
                                                <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'edit-phone-{{ $phone->id }}'}))">Cancel</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </x-modal>

                            {{-- Delete Modal --}}
                            <x-modal name="delete-phone-{{ $phone->id }}" focusable>
                                <div class="p-6">
                                    <h4 class="text-lg font-medium mb-4">Delete Phone</h4>
                                    <div class="mb-4 text-gray-700">
                                        @if($phone->primary)
                                            Are you sure you want to delete {{ $phone->type }} {{ $phone->company->name ?? '' }} {{ $phone->number }}? This is the Contact's Primary Phone Number.
                                        @else
                                            Are you sure you want to delete {{ $phone->type }} {{ $phone->company->name ?? '' }} {{ $phone->number }}?
                                        @endif
                                    </div>
                                    <div class="flex gap-2">
                                        <form action="{{ route('phones.destroy', $phone) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="return_to" value="{{ route('contacts.show', $contact) }}?tab=contact-info" />
                                            <button class="text-red-600">Yes</button>
                                        </form>
                                        <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'delete-phone-{{ $phone->id }}'}))">No</button>
                                    </div>
                                </div>
                            </x-modal>

                        @empty
                            <tr><td colspan="3" class="px-6 py-4">No phone numbers.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endcan

        @can('phones.create')
            <x-modal name="phone-contact-{{ $contact->id }}" focusable>
                <div class="p-6">
                    <h4 class="text-lg font-medium mb-4">Add Phone Number</h4>
                    <form action="{{ route('phones.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="contact_id" value="{{ $contact->id }}" />
                        <input type="hidden" name="return_to" value="{{ route('contacts.show', $contact) }}?tab=contact-info" />

                        @php $hasPhonePrimary = isset($phones) && $phones->firstWhere('primary', 1); @endphp

                        <div class="grid grid-cols-1 gap-2">
                            <input type="text" name="type" class="input" placeholder="Type (e.g. Mobile, Work)" />
                            <select name="company_id" class="input">
                                <option value="">Associate with company (optional)</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                            <input name="number" placeholder="(555) 555-5555" class="input" required />

                            @unless($hasPhonePrimary)
                                <label class="flex items-center gap-2">
                                    <input type="hidden" name="primary" value="0" />
                                    <input type="checkbox" name="primary" value="1" />
                                    <span class="text-sm text-gray-700">Primary</span>
                                </label>
                            @endunless

                            <textarea name="notes" placeholder="Notes" class="input"></textarea>
                            <div class="flex gap-2 mt-4">
                                <button class="btn">Add</button>
                                <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'phone-contact-{{ $contact->id }}'}))">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </x-modal>
        @endcan

    <div class="mt-8">
        <div class="flex items-center justify-between">
            <h3 class="text-md font-semibold mb-2">Email Addresses</h3>
            @can('emails.create')
                <button type="button" class="btn" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'email-contact-{{ $contact->id }}'}))">Add Email</button>
            @endcan
        </div>

        @if(session('email_action'))
            @php
                $emailMessage = null;
                if(session('email_action') === 'added') { $emailMessage = 'A new Email Address has been added.'; }
                elseif(session('email_action') === 'updated') { $emailMessage = 'An Email Address has been updated.'; }
                elseif(session('email_action') === 'deleted') { $emailMessage = 'An Email Address has been deleted.'; }
            @endphp
            @if($emailMessage)
                <div class="mb-4 text-green-600">{{ $emailMessage }}</div>
            @endif
        @endif

        @can('emails.view')
            @php
                $sortedEmails = isset($emails) ? $emails->sortByDesc('primary')->values() : ($contact->emails->sortByDesc('primary')->values() ?? collect());
            @endphp
            <div class="bg-white shadow sm:rounded-lg mb-6">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email Address</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($sortedEmails as $email)
                            <tr>
                                <td class="px-6 py-4 align-top">
                                    <button type="button" class="text-left w-full" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'view-email-{{ $email->id }}'}))">
                                        <div class="font-medium">{{ $email->type ?? 'Email' }} @if($email->primary) <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">Primary</span>@endif</div>
                                        @if($email->company)
                                            <div class="text-sm text-gray-600">{{ $email->company->name }}</div>
                                        @endif
                                    </button>
                                </td>
                                <td class="px-6 py-4 align-top"><a href="mailto:{{ $email->address }}" class="text-blue-600">{{ $email->address }}</a></td>
                                <td class="px-6 py-4 text-right text-sm">
                                    @can('emails.update')
                                        <button type="button" class="text-blue-600 mr-3" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'edit-email-{{ $email->id }}'}))">Edit</button>
                                    @endcan
                                    @can('emails.delete')
                                        <button type="button" class="text-red-600" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'delete-email-{{ $email->id }}'}))">Delete</button>
                                    @endcan
                                </td>
                            </tr>

                            {{-- View Modal --}}
                            <x-modal name="view-email-{{ $email->id }}" focusable>
                                <div class="p-6">
                                    <h4 class="text-lg font-medium mb-4">View Email</h4>
                                    <div class="grid grid-cols-1 gap-2 mb-4">
                                        <div><strong>Type</strong><div class="text-gray-700">{{ $email->type }}</div></div>
                                        @if($email->company)
                                            <div><strong>Company</strong><div class="text-gray-700">{{ $email->company->name }}</div></div>
                                        @endif
                                        <div><strong>Email Address</strong><div class="text-gray-700">{{ $email->address }}</div></div>
                                        <div><strong>Notes</strong><div class="text-gray-700">{{ $email->notes ?? '—' }}</div></div>
                                    </div>
                                    <div class="flex gap-2">
                                        @can('emails.update')
                                            <button type="button" class="btn" onclick="(function(){window.dispatchEvent(new CustomEvent('close-modal',{detail:'view-email-{{ $email->id }}'}));window.dispatchEvent(new CustomEvent('open-modal',{detail:'edit-email-{{ $email->id }}'}));})();">Edit</button>
                                        @endcan
                                        <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'view-email-{{ $email->id }}'}))">Close</button>
                                    </div>
                                </div>
                            </x-modal>

                            {{-- Edit Modal --}}
                            <x-modal name="edit-email-{{ $email->id }}" focusable>
                                <div class="p-6">
                                    <h4 class="text-lg font-medium mb-4">Edit Email</h4>
                                    <form action="{{ route('emails.update', $email) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="contact_id" value="{{ $contact->id }}" />
                                        <input type="hidden" name="return_to" value="{{ route('contacts.show', $contact) }}?tab=contact-info" />

                                        <div class="grid grid-cols-1 gap-2">
                                            <input name="type" value="{{ $email->type }}" class="input" placeholder="Type (e.g. Work)" />
                                            <select name="company_id" class="input">
                                                <option value="">Associate with company (optional)</option>
                                                @foreach($companies as $company)
                                                    <option value="{{ $company->id }}" {{ $email->company_id == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                                                @endforeach
                                            </select>
                                            <input name="address" value="{{ $email->address }}" placeholder="name@example.com" class="input" required />
                                            <label class="flex items-center gap-2">
                                                <input type="hidden" name="primary" value="0" />
                                                <input type="checkbox" name="primary" value="1" {{ $email->primary ? 'checked' : '' }} />
                                                <span class="text-sm text-gray-700">Primary</span>
                                            </label>
                                            <textarea name="notes" class="input" placeholder="Notes">{{ $email->notes }}</textarea>
                                            <div class="flex gap-2 mt-4">
                                                <button class="btn">Save</button>
                                                <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'edit-email-{{ $email->id }}'}))">Cancel</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </x-modal>

                            {{-- Delete Modal --}}
                            <x-modal name="delete-email-{{ $email->id }}" focusable>
                                <div class="p-6">
                                    <h4 class="text-lg font-medium mb-4">Delete Email</h4>
                                    <div class="mb-4 text-gray-700">
                                        @if($email->primary)
                                            Are you sure you want to delete {{ $email->type }} {{ $email->company->name ?? '' }} {{ $email->address }}? This is the Contact's Primary Email Address.
                                        @else
                                            Are you sure you want to delete {{ $email->type }} {{ $email->company->name ?? '' }} {{ $email->address }}?
                                        @endif
                                    </div>
                                    <div class="flex gap-2">
                                        <form action="{{ route('emails.destroy', $email) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="return_to" value="{{ route('contacts.show', $contact) }}?tab=contact-info" />
                                            <button class="text-red-600">Yes</button>
                                        </form>
                                        <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'delete-email-{{ $email->id }}'}))">No</button>
                                    </div>
                                </div>
                            </x-modal>

                        @empty
                            <tr><td colspan="3" class="px-6 py-4">No email addresses.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endcan

        @can('emails.create')
            <x-modal name="email-contact-{{ $contact->id }}" focusable>
                <div class="p-6">
                    <h4 class="text-lg font-medium mb-4">Add Email Address</h4>
                    <form action="{{ route('emails.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="contact_id" value="{{ $contact->id }}" />
                        <input type="hidden" name="return_to" value="{{ route('contacts.show', $contact) }}?tab=contact-info" />

                        @php $hasPrimary = isset($emails) && $emails->firstWhere('primary', 1); @endphp

                        <div class="grid grid-cols-1 gap-2">
                            <input type="text" name="type" class="input" placeholder="Type (e.g. Work, Personal)" />
                            <select name="company_id" class="input">
                                <option value="">Associate with company (optional)</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                            <input name="address" placeholder="name@example.com" class="input" required />

                            @unless($hasPrimary)
                                <label class="flex items-center gap-2">
                                    <input type="hidden" name="primary" value="0" />
                                    <input type="checkbox" name="primary" value="1" />
                                    <span class="text-sm text-gray-700">Primary</span>
                                </label>
                            @endunless

                            <textarea name="notes" placeholder="Notes" class="input"></textarea>
                            <div class="flex gap-2 mt-4">
                                <button class="btn">Add</button>
                                <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'email-contact-{{ $contact->id }}'}))">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </x-modal>
        @endcan
    </div>

    </div> <!-- end tab-contact-info -->

    <div id="tab-notes" class="tab-panel hidden">
        <div class="mt-4">
            <h3 class="text-md font-semibold mb-2">Notes</h3>
            <div class="mt-2 whitespace-pre-wrap text-gray-700">{{ $contact->notes ?? '—' }}</div>
        </div>
    </div>

    <div id="tab-files" class="tab-panel hidden">
        <div class="mt-4 text-gray-600">
            Files to be added later.
        </div>
    </div>

    <script>
        (function(){
            function activateTab(name){
                document.querySelectorAll('.tab-panel').forEach(function(p){ p.classList.add('hidden'); });
                document.querySelectorAll('.tab-btn').forEach(function(b){ b.classList.remove('border-blue-600','text-blue-600'); b.classList.add('text-gray-700'); b.classList.remove('font-semibold'); });
                var panel = document.getElementById('tab-' + name);
                if(panel) panel.classList.remove('hidden');
                var btn = document.querySelector('.tab-btn[data-tab="' + name + '"]');
                if(btn){ btn.classList.add('border-blue-600','text-blue-600','font-semibold'); }
            }

            document.querySelectorAll('.tab-btn').forEach(function(btn){
                btn.addEventListener('click', function(){ activateTab(btn.dataset.tab); });
            });

            // default — check URL for ?tab= parameter, otherwise 'overview'
            document.addEventListener('DOMContentLoaded', function(){
                var params = new URLSearchParams(window.location.search);
                var tab = params.get('tab') || 'overview';
                activateTab(tab);
            });

            // When any modal is closed, stay on the current tab (do nothing)
        })();
    </script>

            </div>
        </div>

    </div>
    @endsection
