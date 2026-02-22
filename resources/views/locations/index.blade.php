<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Locations') }}</h2>
            @can('venues.create')
                <a href="{{ route('venues.create') }}" class="btn">New Venue</a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if(session('success'))
                        <div class="mb-4 text-green-600">{{ session('success') }}</div>
                    @endif

                    <table class="w-full table-auto">
                        <thead>
                            <tr class="text-left">
                                <th class="px-2 py-2">&nbsp;</th>
                                <th class="px-2 py-2">Name</th>
                                <th class="px-2 py-2">Type</th>
                                <th class="px-2 py-2">Address</th>
                                <th class="px-2 py-2">Spaces</th>
                                <th class="px-2 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Venues --}}
                            @forelse($venues as $venue)
                                <tr class="border-t bg-gray-50" data-venue-id="{{ $venue->id }}">
                                    <td class="px-2 py-2">
                                        <button class="toggle-venue" data-id="{{ $venue->id }}" aria-label="Toggle venue">▼</button>
                                    </td>
                                    <td class="px-2 py-2"><a href="{{ route('venues.show', $venue) }}" class="text-blue-600 font-medium">{{ $venue->name }}</a></td>
                                    <td class="px-2 py-2">{{ $venue->type ?? 'Venue' }}</td>
                                    <td class="px-2 py-2 text-sm text-gray-600">{{ optional($venue->primaryAddress)->address1 ?? '—' }}{{ optional($venue->primaryAddress)->city ? ', '.optional($venue->primaryAddress)->city : '' }}</td>
                                    <td class="px-2 py-2">@php $spacesCount = 0; foreach($venue->buildings as $b) $spacesCount += $b->spaces->count(); @endphp {{ $spacesCount }}</td>
                                    <td class="px-2 py-2">
                                        @can('venues.update')<a href="{{ route('venues.edit', $venue) }}" class="ml-2 text-blue-600">Edit</a>@endcan
                                        @can('venues.delete')
                                            <form action="{{ route('venues.destroy', $venue) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Delete venue?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600">Delete</button>
                                            </form>
                                        @endcan
                                        @can('buildings.create')<button class="ml-2 text-sm text-green-600 add-building" data-venue-id="{{ $venue->id }}">Add Building</button>@endcan
                                    </td>
                                </tr>

                                {{-- Buildings for this venue --}}
                                @foreach($venue->buildings as $building)
                                    <tr class="border-t child-of-venue-{{ $venue->id }} hidden bg-white">
                                        <td class="px-2 py-2"></td>
                                        <td class="px-2 py-2 pl-8">
                                            <button class="toggle-building" data-id="{{ $building->id }}">▶</button>
                                            <a href="{{ route('buildings.show', $building) }}" class="text-blue-600">{{ $building->name }}</a>
                                        </td>
                                        <td class="px-2 py-2">{{ $building->type ?? 'Building' }}</td>
                                        <td class="px-2 py-2 text-sm text-gray-600">{{ optional($building->primaryAddress)->address1 ?? '—' }}{{ optional($building->primaryAddress)->city ? ', '.optional($building->primaryAddress)->city : '' }}</td>
                                        <td class="px-2 py-2">{{ $building->spaces->count() }}</td>
                                        <td class="px-2 py-2">
                                            @can('buildings.update')<a href="{{ route('buildings.edit', $building) }}" class="ml-2 text-blue-600">Edit</a>@endcan
                                            @can('buildings.delete')
                                                <form action="{{ route('buildings.destroy', $building) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Delete building?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600">Delete</button>
                                                </form>
                                            @endcan
                                            @can('spaces.create')<button class="ml-2 text-sm text-green-600 add-space" data-building-id="{{ $building->id }}">Add Space</button>@endcan
                                        </td>
                                    </tr>

                                    {{-- Spaces for this building --}}
                                    @foreach($building->spaces as $space)
                                        <tr class="border-t child-of-building-{{ $building->id }} hidden bg-gray-50">
                                            <td class="px-2 py-2"></td>
                                            <td class="px-2 py-2 pl-16"><a href="{{ route('spaces.show', $space) }}" class="text-blue-600">{{ $space->name }}</a></td>
                                            <td class="px-2 py-2">{{ $space->type }}</td>
                                            <td class="px-2 py-2 text-sm text-gray-600">{{ optional($space->primaryAddress)->address1 ?? '—' }}{{ optional($space->primaryAddress)->city ? ', '.optional($space->primaryAddress)->city : '' }}</td>
                                            <td class="px-2 py-2">—</td>
                                            <td class="px-2 py-2">
                                                @can('spaces.update')<a href="{{ route('spaces.edit', $space) }}" class="ml-2 text-blue-600">Edit</a>@endcan
                                                @can('spaces.delete')
                                                    <form action="{{ route('spaces.destroy', $space) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Delete space?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600">Delete</button>
                                                    </form>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            @empty
                                <tr>
                                    <td class="px-2 py-4" colspan="6">No locations found.</td>
                                </tr>
                            @endforelse

                            {{-- Orphan buildings --}}
                            @if(!empty($orphanBuildings) && $orphanBuildings->count())
                                <tr class="border-t bg-gray-100"><td></td><td colspan="5" class="px-2 py-2 font-medium">Orphan Buildings</td></tr>
                                @foreach($orphanBuildings as $building)
                                    <tr class="border-t bg-white">
                                        <td class="px-2 py-2"></td>
                                        <td class="px-2 py-2 pl-8"><a href="{{ route('buildings.show', $building) }}" class="text-blue-600">{{ $building->name }}</a></td>
                                        <td class="px-2 py-2">{{ $building->type ?? 'Building' }}</td>
                                        <td class="px-2 py-2 text-sm text-gray-600">{{ optional($building->primaryAddress)->address1 ?? '—' }}{{ optional($building->primaryAddress)->city ? ', '.optional($building->primaryAddress)->city : '' }}</td>
                                        <td class="px-2 py-2">{{ $building->spaces->count() }}</td>
                                        <td class="px-2 py-2">
                                            @can('buildings.update')<a href="{{ route('buildings.edit', $building) }}" class="ml-2 text-blue-600">Edit</a>@endcan
                                            @can('buildings.delete')
                                                <form action="{{ route('buildings.destroy', $building) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Delete building?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600">Delete</button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            @endif

                            {{-- Orphan spaces --}}
                            @if(!empty($orphanSpaces) && $orphanSpaces->count())
                                <tr class="border-t bg-gray-100"><td></td><td colspan="5" class="px-2 py-2 font-medium">Orphan Spaces</td></tr>
                                @foreach($orphanSpaces as $space)
                                    <tr class="border-t bg-white">
                                        <td class="px-2 py-2"></td>
                                        <td class="px-2 py-2 pl-8"><a href="{{ route('spaces.show', $space) }}" class="text-blue-600">{{ $space->name }}</a></td>
                                        <td class="px-2 py-2">{{ $space->type }}</td>
                                        <td class="px-2 py-2 text-sm text-gray-600">{{ optional($space->primaryAddress)->address1 ?? '—' }}{{ optional($space->primaryAddress)->city ? ', '.optional($space->primaryAddress)->city : '' }}</td>
                                        <td class="px-2 py-2">—</td>
                                        <td class="px-2 py-2">
                                            @can('spaces.update')<a href="{{ route('spaces.edit', $space) }}" class="ml-2 text-blue-600">Edit</a>@endcan
                                            @can('spaces.delete')
                                                <form action="{{ route('spaces.destroy', $space) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Delete space?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600">Delete</button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            @endif

                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>

    <script>
        (function(){
            // Toggle venue rows
            document.querySelectorAll('.toggle-venue').forEach(btn=>{
                btn.addEventListener('click', function(){
                    const id = this.dataset.id;
                    const children = document.querySelectorAll('.child-of-venue-'+id);
                    children.forEach(c=> c.classList.toggle('hidden'));
                    this.textContent = this.textContent.trim() === '▼' ? '▲' : '▼';
                });
            });

            // Toggle building rows
            document.querySelectorAll('.toggle-building').forEach(btn=>{
                btn.addEventListener('click', function(){
                    const id = this.dataset.id;
                    const children = document.querySelectorAll('.child-of-building-'+id);
                    children.forEach(c=> c.classList.toggle('hidden'));
                    this.textContent = this.textContent.trim() === '▶' ? '▼' : '▶';
                });
            });

            // Add Building / Space handlers (open create route in modal)
            function openModalWithForm(url){
                let modal = document.getElementById('crud-modal');
                if(!modal){
                    modal = document.createElement('div');
                    modal.id = 'crud-modal';
                    modal.innerHTML = '<div class="fixed inset-0 bg-black bg-opacity-50 flex items-start justify-center p-6"><div class="bg-white dark:bg-gray-800 rounded shadow max-w-2xl w-full p-4" id="crud-modal-content"><button id="crud-modal-close" class="float-right text-gray-600">Close</button><div id="crud-modal-body">Loading…</div></div></div>';
                    document.body.appendChild(modal);
                    document.getElementById('crud-modal-close').addEventListener('click', ()=> modal.remove());
                }
                const body = modal.querySelector('#crud-modal-body');
                fetch(url, {headers:{'X-Requested-With':'XMLHttpRequest'}}).then(r=>r.text()).then(html=>{
                    const formMatch = html.match(/<form[\s\S]*?>[\s\S]*?<\/form>/i);
                    if(formMatch) body.innerHTML = formMatch[0]; else body.innerHTML = html;
                }).catch(e=>{ body.innerHTML = 'Failed to load form'; });
            }

            document.querySelectorAll('.add-building').forEach(btn=>{
                btn.addEventListener('click', function(){
                    const venueId = this.dataset.venueId;
                    openModalWithForm('/buildings/create?venue_id='+venueId);
                });
            });

            document.querySelectorAll('.add-space').forEach(btn=>{
                btn.addEventListener('click', function(){
                    const buildingId = this.dataset.buildingId;
                    openModalWithForm('/spaces/create?building_id='+buildingId);
                });
            });
        })();
    </script>
</x-app-layout>
