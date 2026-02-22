<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ $show->title }}</h2>
            <div>
                @can('shows.update')
                    <a href="{{ route('shows.edit', $show) }}" class="text-blue-600">Edit</a>
                @endcan
                <a href="{{ route('shows.index') }}" class="ml-4 text-gray-600">Back</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if($show->choreography_by)
                        <div class="mb-4"><strong>Choreography By:</strong> {{ $show->choreography_by }}</div>
                    @endif
                    <div class="mb-4"><strong>Season:</strong> {{ $show->season->name }}</div>
                    <div class="mb-4"><strong>Opening:</strong> {{ optional($show->opening_date)->toDateString() ?? '—' }}</div>
                    <div class="mb-4"><strong>Status:</strong> {{ $show->status }}</div>
                    <div class="mb-6"><strong>Notes</strong><div class="mt-2 text-gray-700">{{ $show->notes ?? '—' }}</div></div>

                    <div class="mb-4">
                        <div class="flex items-center justify-between">
                            <strong>Productions</strong>
                            @can('productions.create')
                                <a href="{{ route('productions.create', ['show_id' => $show->id]) }}" class="text-blue-600">New Production</a>
                            @endcan
                        </div>

                        <div class="mt-2 space-y-4">
                            @foreach($show->productions as $p)
                                <div class="border p-3 rounded" id="production-{{ $p->id }}">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <div class="font-medium"><a href="{{ route('productions.show', $p) }}" class="text-blue-600">{{ $p->title }}</a></div>
                                            <div class="text-sm text-gray-600">{{ $p->status }} — {{ optional($p->start_date)->toDateString() ?? '—' }} to {{ optional($p->end_date)->toDateString() ?? '—' }}</div>
                                            @if($p->notes)
                                                <div class="mt-2 text-sm">{{ $p->notes }}</div>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            @can('productions.update')
                                                <a href="{{ route('productions.edit', $p) }}" class="text-sm text-blue-600">Edit</a>
                                            @endcan
                                            @can('productions.delete')
                                                <form action="{{ route('productions.destroy', $p) }}" method="POST" class="inline ml-2">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="text-sm text-red-600">Delete</button>
                                                </form>
                                            @endcan
                                        </div>
                                    </div>

                                    <div id="prod-edit-{{ $p->id }}" style="display:none;" class="mt-4">
                                        @can('productions.update')
                                            <form action="{{ route('productions.update', $p) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="show_id" value="{{ $show->id }}" />
                                                <div class="grid gap-2">
                                                    <input name="title" class="input" value="{{ old('title', $p->title) }}" />
                                                    <input name="status" class="input" value="{{ old('status', $p->status) }}" />
                                                    <div class="flex gap-2">
                                                        <input type="date" name="start_date" class="input flex-1" value="{{ old('start_date', optional($p->start_date)->toDateString()) }}" />
                                                        <input type="date" name="end_date" class="input flex-1" value="{{ old('end_date', optional($p->end_date)->toDateString()) }}" />
                                                    </div>
                                                    <label class="block">
                                                        <span class="text-sm">Companies</span>
                                                        <select name="companies[]" multiple class="input mt-1 block w-full">
                                                            @foreach($companies as $c)
                                                                <option value="{{ $c->id }}" {{ in_array($c->id, $p->companies->pluck('id')->toArray()) ? 'selected' : '' }}>{{ $c->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </label>
                                                    <label class="block">
                                                        <span class="text-sm">Contacts</span>
                                                        <select name="contacts[]" multiple class="input mt-1 block w-full">
                                                            @foreach($contacts as $ct)
                                                                <option value="{{ $ct->id }}" {{ in_array($ct->id, $p->contacts->pluck('id')->toArray()) ? 'selected' : '' }}>{{ trim(($ct->first_name ?? '') . ' ' . ($ct->last_name ?? '')) }}</option>
                                                            @endforeach
                                                        </select>
                                                    </label>
                                                    <textarea name="notes" class="input">{{ old('notes', $p->notes) }}</textarea>
                                                    <div class="flex gap-2">
                                                        <button class="btn">Save</button>
                                                        <button type="button" class="ml-2 text-gray-600" onclick="document.getElementById('prod-edit-{{ $p->id }}').style.display='none';document.getElementById('prod-view-{{ $p->id }}').style.display='block'">Cancel</button>
                                                    </div>
                                                </div>
                                            </form>
                                        @endcan
                                    </div>

                                    <div id="prod-view-{{ $p->id }}" style="display:block;" class="mt-4"></div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
