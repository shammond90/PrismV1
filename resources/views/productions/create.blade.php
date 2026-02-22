<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('New Production') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @can('productions.create')
                        <form action="{{ route('productions.store') }}" method="POST">
                            @csrf
                            <div class="grid gap-4">
                                <label class="block"><span class="text-sm text-gray-700">Show</span>
                                    <select name="show_id" class="input mt-1 block w-full">
                                        @foreach($shows as $show)
                                            <option value="{{ $show->id }}" {{ (old('show_id') == $show->id) || (request()->get('show_id') == $show->id) ? 'selected' : '' }}>{{ $show->title }} ({{ optional($show->opening_date)->toDateString() }})</option>
                                        @endforeach
                                    </select>
                                </label>

                                <label class="block"><span class="text-sm text-gray-700">Title</span><input name="title" value="{{ old('title') }}" class="input mt-1 block w-full" /></label>
                                <label class="block"><span class="text-sm text-gray-700">Start Date</span><input type="date" name="start_date" value="{{ old('start_date') }}" class="input mt-1 block w-full" /></label>
                                <label class="block"><span class="text-sm text-gray-700">End Date</span><input type="date" name="end_date" value="{{ old('end_date') }}" class="input mt-1 block w-full" /></label>
                                <label class="block"><span class="text-sm text-gray-700">Initial Contact Date</span><input type="date" name="initial_contact_date" value="{{ old('initial_contact_date') }}" class="input mt-1 block w-full" /></label>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                    <label class="block"><span class="text-sm text-gray-700">Venue (filter)</span>
                                        <select id="venue-select" class="input mt-1 block w-full">
                                            <option value="">Any</option>
                                            @foreach($venues as $v)
                                                <option value="{{ $v->id }}">{{ $v->name }}</option>
                                            @endforeach
                                        </select>
                                    </label>

                                    <label class="block"><span class="text-sm text-gray-700">Building (filter)</span>
                                        <select id="building-select" class="input mt-1 block w-full">
                                            <option value="">Any</option>
                                            @foreach($buildings as $b)
                                                <option value="{{ $b->id }}" data-venue="{{ $b->venue_id }}">{{ $b->name }}</option>
                                            @endforeach
                                        </select>
                                    </label>

                                    <label class="block"><span class="text-sm text-gray-700">Space</span>
                                        <select name="space_id" id="space-select" class="input mt-1 block w-full">
                                            <option value="">None</option>
                                            @foreach($spaces as $s)
                                                <option value="{{ $s->id }}" data-building="{{ $s->building_id }}" data-building-name="{{ $s->building->name ?? '' }}" data-space-name="{{ $s->name }}">@if($s->building){{ $s->building->name }} - {{ $s->name }}@else{{ $s->name }}@endif</option>
                                            @endforeach
                                        </select>
                                    </label>
                                </div>
                                <label class="block"><span class="text-sm text-gray-700">Primary Company (optional)</span>
                                    <select name="primary_company_id" class="input mt-1 block w-full">
                                        <option value="">None</option>
                                        @foreach($companies as $company)
                                            <option value="{{ $company->id }}" {{ old('primary_company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                                        @endforeach
                                    </select>
                                </label>
                                <label class="block"><span class="text-sm text-gray-700">Primary Contact (optional)</span>
                                    <select name="primary_contact_id" class="input mt-1 block w-full">
                                        <option value="">None</option>
                                        @foreach($contacts as $contact)
                                            <option value="{{ $contact->id }}" {{ old('primary_contact_id') == $contact->id ? 'selected' : '' }}>{{ trim(($contact->first_name ?? '') . ' ' . ($contact->last_name ?? '')) ?: 'Contact #' . $contact->id }}</option>
                                        @endforeach
                                    </select>
                                </label>
                                <label class="block"><span class="text-sm text-gray-700">Notes</span><textarea name="notes" class="input mt-1 block w-full">{{ old('notes') }}</textarea></label>
                                <div class="flex gap-2"><button class="btn">Create</button><a href="{{ route('productions.index') }}" class="ml-2 text-gray-600">Cancel</a></div>
                            </div>
                        </form>
                    @else
                        <div class="text-red-600">You do not have permission to create productions.</div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
    <script>
        (function(){
            const venue = document.getElementById('venue-select');
            const building = document.getElementById('building-select');
            const space = document.getElementById('space-select');

            function filterBuildings(){
                const v = venue.value;
                Array.from(building.options).forEach(opt => {
                    if(!opt.value) return; // keep Any
                    opt.style.display = (!v || opt.dataset.venue === v) ? '' : 'none';
                });
            }

            function filterSpaces(){
                const b = building.value;
                const showBuildingInLabel = !b; // when no building selected, include building name in label
                Array.from(space.options).forEach(opt => {
                    if(!opt.value) return;
                    const matches = (!b || opt.dataset.building === b);
                    opt.style.display = matches ? '' : 'none';
                    const spaceName = opt.dataset.spaceName || opt.textContent;
                    const buildingName = opt.dataset.buildingName || '';
                    opt.text = showBuildingInLabel ? (buildingName ? (buildingName + ' - ' + spaceName) : spaceName) : spaceName;
                });
            }

            venue.addEventListener('change', function(){ filterBuildings(); building.value=''; filterSpaces(); });
            building.addEventListener('change', function(){ filterSpaces(); });

            // initialize labels based on current filter state
            filterBuildings();
            filterSpaces();
        })();
    </script>
</x-app-layout>
