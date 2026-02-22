<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Edit Show') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @can('shows.update')
                        <form action="{{ route('shows.update', $show) }}" method="POST">
                            @csrf
                            @method('PUT')
                        <div class="grid gap-4">
                            <label class="block">
                                <span class="text-sm text-gray-700">Season</span>
                                <select name="season_id" class="input mt-1 block w-full">
                                    @foreach($seasons as $season)
                                        <option value="{{ $season->id }}" {{ $season->id === $show->season_id ? 'selected' : '' }}>{{ $season->name }}</option>
                                    @endforeach
                                </select>
                            </label>

                            <label class="block">
                                <span class="text-sm text-gray-700">Show Catalogue (optional)</span>
                                <select name="show_catalogue_id" id="show_catalogue_id" class="input mt-1 block w-full">
                                    <option value="">— none —</option>
                                    @foreach($catalogues as $cat)
                                        <option value="{{ $cat->id }}" data-title="{{ e($cat->title) }}" data-description="{{ e($cat->description) }}" data-choreography="{{ e($cat->choreography_by) }}" {{ $cat->id === ($show->show_catalogue_id ?? null) ? 'selected' : '' }}>{{ $cat->title }}@if($cat->choreography_by) By {{ $cat->choreography_by }}@endif</option>
                                    @endforeach
                                </select>
                            </label>

                            <label class="block">
                                <span class="text-sm text-gray-700">Title</span>
                                <input id="show_title" name="title" value="{{ old('title', $show->title) }}" class="input mt-1 block w-full" />
                            </label>

                            <label class="block">
                                <span class="text-sm text-gray-700">Choreography By</span>
                                <input name="choreography_by" value="{{ old('choreography_by', $show->choreography_by) }}" class="input mt-1 block w-full" placeholder="e.g. Choreographer or Company" />
                            </label>
                            
                            <div class="flex gap-2">
                                <label class="block flex-1">
                                    <span class="text-sm text-gray-700">Opening Date</span>
                                    <input type="date" name="opening_date" value="{{ old('opening_date', optional($show->opening_date)->toDateString()) }}" class="input mt-1 block w-full" />
                                </label>
                            </div>

                            <label class="block">
                                <span class="text-sm text-gray-700">Status</span>
                                <input name="status" value="{{ old('status', $show->status) }}" class="input mt-1 block w-full" />
                            </label>

                            <!-- Companies and Contacts moved to Production level -->

                            <label class="block">
                                <span class="text-sm text-gray-700">Notes</span>
                                <textarea id="show_notes" name="notes" class="input mt-1 block w-full">{{ old('notes', $show->notes) }}</textarea>
                            </label>

                            <div class="flex gap-2">
                                <button class="btn">Save</button>
                                <a href="{{ route('shows.show', $show) }}" class="ml-2 text-gray-600">Cancel</a>
                            </div>
                        </div>
                        </form>
                    @else
                        <div class="text-red-600">You do not have permission to edit this show.</div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
    <script>
        (function(){
            const sel = document.getElementById('show_catalogue_id');
            if (!sel) return;
            sel.addEventListener('change', function(){
                const opt = sel.options[sel.selectedIndex];
                    const title = opt ? opt.getAttribute('data-title') || '' : '';
                    const desc = opt ? opt.getAttribute('data-description') || '' : '';
                    const choreography = opt ? opt.getAttribute('data-choreography') || '' : '';
                const titleInput = document.getElementById('show_title');
                const notesInput = document.getElementById('show_notes');
                    if (titleInput && title) titleInput.value = title;
                    if (notesInput && desc) notesInput.value = desc;
                    if (choreography && document.querySelector('[name="choreography_by"]')) {
                        document.querySelector('[name="choreography_by"]').value = choreography;
                    }
            });
        })();
    </script>
</x-app-layout>
