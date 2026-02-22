@extends('layouts.app')

@php
use Illuminate\Support\Str;
@endphp

@section('content')
<div class="max-w-5xl mx-auto py-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="font-semibold text-xl text-gray-800">{{ $productionTemplate->name }}</h2>
        <div>
            @can('show_catalogues.update')
                <button type="button" class="text-blue-600 mr-3" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'edit-template'}))">Edit</button>
            @endcan
            <a href="{{ route('show_catalogues.show', $showCatalogue) }}?tab=production-catalogue" class="text-gray-600">Back to Catalogue</a>
        </div>
    </div>

    @if(session('success') && !request('tab'))
        <div class="mb-4 text-green-600">{{ session('success') }}</div>
    @endif

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">

            {{-- ===== Tab Navigation ===== --}}
            <div class="mb-4 border-b">
                <nav class="-mb-px flex space-x-6 overflow-x-auto" aria-label="Tabs">
                    <button data-tab="overview" class="tab-btn pb-2 text-sm font-medium text-gray-700 border-b-2 border-transparent whitespace-nowrap">Overview</button>
                    <button data-tab="baseline-paperwork" class="tab-btn pb-2 text-sm font-medium text-gray-700 border-b-2 border-transparent whitespace-nowrap">Baseline Paperwork</button>
                    <button data-tab="baseline-notes" class="tab-btn pb-2 text-sm font-medium text-gray-700 border-b-2 border-transparent whitespace-nowrap">Baseline Notes</button>
                    <button data-tab="baseline-staffing" class="tab-btn pb-2 text-sm font-medium text-gray-700 border-b-2 border-transparent whitespace-nowrap">Baseline Staffing</button>
                    <button data-tab="baseline-schedules" class="tab-btn pb-2 text-sm font-medium text-gray-700 border-b-2 border-transparent whitespace-nowrap">Baseline Schedule Templates</button>
                    <button data-tab="files" class="tab-btn pb-2 text-sm font-medium text-gray-700 border-b-2 border-transparent whitespace-nowrap">Files</button>
                </nav>
            </div>

            {{-- ================================================================ --}}
            {{-- TAB 1: Overview --}}
            {{-- ================================================================ --}}
            <div id="tab-overview" class="tab-panel">
                <div class="mb-4">
                    <strong>Show Catalogue:</strong>
                    <a href="{{ route('show_catalogues.show', $showCatalogue) }}" class="text-blue-600">{{ $showCatalogue->title }}</a>
                </div>
                <div class="mb-4"><strong>Production Name:</strong> {{ $productionTemplate->name }}</div>
                <div class="mb-4"><strong>Description:</strong>
                    <div class="mt-1 text-gray-700 whitespace-pre-wrap">{{ $productionTemplate->description ?? '—' }}</div>
                </div>
                <div class="mb-4"><strong>Notes:</strong>
                    <div class="mt-1 text-gray-700 whitespace-pre-wrap">{{ $productionTemplate->notes ?? '—' }}</div>
                </div>
                <div class="mb-4"><strong>Last Updated:</strong> {{ $productionTemplate->updated_at->toDateString() }}</div>
                <div class="mb-4"><strong>Tags:</strong>
                    @if(!empty($productionTemplate->tags))
                        @foreach($productionTemplate->tags as $tag)
                            <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-0.5 rounded mr-1">{{ $tag }}</span>
                        @endforeach
                    @else
                        <span class="text-gray-500">—</span>
                    @endif
                </div>

                @can('show_catalogues.update')
                    <div class="flex gap-3 mt-6">
                        <button type="button" class="btn" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'edit-template'}))">Edit Template</button>
                        <button type="button" class="text-red-600" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'delete-template'}))">Delete Template</button>
                    </div>
                @endcan
            </div>

            {{-- ================================================================ --}}
            {{-- TAB 2: Baseline Paperwork --}}
            {{-- ================================================================ --}}
            <div id="tab-baseline-paperwork" class="tab-panel hidden">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-md font-semibold">Baseline Paperwork</h3>
                    @can('show_catalogues.update')
                        <button type="button" class="btn" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'add-tpl-paperwork'}))">Add Paperwork</button>
                    @endcan
                </div>

                @if(session('success') && request('tab') === 'baseline-paperwork')
                    <div class="mb-4 text-green-600">{{ session('success') }}</div>
                @endif

                <div class="bg-white shadow sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($productionTemplate->paperwork as $pw)
                                <tr>
                                    <td class="px-6 py-4 text-sm">{{ $pw->title }}</td>
                                    <td class="px-6 py-4 text-sm">{{ $pw->department }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        <a href="{{ route('production_templates.paperwork.download', [$showCatalogue, $productionTemplate, $pw]) }}" class="text-blue-600">{{ $pw->original_filename }}</a>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ Str::limit($pw->notes, 60) }}</td>
                                    <td class="px-6 py-4 text-right text-sm">
                                        @can('show_catalogues.update')
                                            <button type="button" class="text-blue-600 mr-3" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'view-tpw-{{ $pw->id }}'}))">View</button>
                                            <button type="button" class="text-blue-600 mr-3" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'edit-tpw-{{ $pw->id }}'}))">Edit</button>
                                            <button type="button" class="text-red-600" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'delete-tpw-{{ $pw->id }}'}))">Delete</button>
                                        @endcan
                                    </td>
                                </tr>

                                {{-- View Paperwork Modal --}}
                                <x-modal name="view-tpw-{{ $pw->id }}" focusable>
                                    <div class="p-6">
                                        <h4 class="text-lg font-medium mb-4">View Paperwork</h4>
                                        <div class="grid grid-cols-1 gap-2 mb-4">
                                            <div><strong>Title:</strong> {{ $pw->title }}</div>
                                            <div><strong>Department:</strong> {{ $pw->department }}</div>
                                            <div><strong>File:</strong> <a href="{{ route('production_templates.paperwork.download', [$showCatalogue, $productionTemplate, $pw]) }}" class="text-blue-600">{{ $pw->original_filename }}</a></div>
                                            <div><strong>Notes:</strong><div class="mt-1 text-gray-700 whitespace-pre-wrap">{{ $pw->notes ?? '—' }}</div></div>
                                        </div>
                                        <div class="flex gap-2">
                                            @can('show_catalogues.update')
                                                <button type="button" class="btn" onclick="(function(){window.dispatchEvent(new CustomEvent('close-modal',{detail:'view-tpw-{{ $pw->id }}'}));window.dispatchEvent(new CustomEvent('open-modal',{detail:'edit-tpw-{{ $pw->id }}'}));})();">Edit</button>
                                            @endcan
                                            <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'view-tpw-{{ $pw->id }}'}))">Close</button>
                                        </div>
                                    </div>
                                </x-modal>

                                {{-- Edit Paperwork Modal --}}
                                <x-modal name="edit-tpw-{{ $pw->id }}" focusable>
                                    <div class="p-6">
                                        <h4 class="text-lg font-medium mb-4">Edit Paperwork</h4>
                                        <form action="{{ route('production_templates.paperwork.update', [$showCatalogue, $productionTemplate, $pw]) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <div class="grid grid-cols-1 gap-3">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                                                    <input name="title" value="{{ $pw->title }}" class="input w-full" required />
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                                                    <select name="department" class="input w-full" required>
                                                        <option value="">Select department...</option>
                                                        @foreach($departments as $dept)
                                                            <option value="{{ $dept->name }}" {{ $pw->department === $dept->name ? 'selected' : '' }}>{{ $dept->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Replace File (optional)</label>
                                                    <input type="file" name="file" class="input w-full" />
                                                    <p class="text-xs text-gray-500 mt-1">Current: {{ $pw->original_filename }}</p>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                                    <textarea name="notes" rows="3" class="input w-full">{{ $pw->notes }}</textarea>
                                                </div>
                                                <div class="flex gap-2 mt-2">
                                                    <button type="submit" class="btn">Save</button>
                                                    <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'edit-tpw-{{ $pw->id }}'}))">Cancel</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </x-modal>

                                {{-- Delete Paperwork Modal --}}
                                <x-modal name="delete-tpw-{{ $pw->id }}" focusable>
                                    <div class="p-6">
                                        <h4 class="text-lg font-medium mb-4">Delete Paperwork</h4>
                                        <p class="mb-4 text-gray-700">Are you sure you want to delete <strong>{{ $pw->title }}</strong>?</p>
                                        <div class="flex gap-2">
                                            <form action="{{ route('production_templates.paperwork.destroy', [$showCatalogue, $productionTemplate, $pw]) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-red-600">Yes, Delete</button>
                                            </form>
                                            <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'delete-tpw-{{ $pw->id }}'}))">Cancel</button>
                                        </div>
                                    </div>
                                </x-modal>
                            @empty
                                <tr><td colspan="5" class="px-6 py-4 text-gray-500">No paperwork records.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Add Paperwork Modal --}}
                @can('show_catalogues.update')
                    <x-modal name="add-tpl-paperwork" focusable>
                        <div class="p-6">
                            <h4 class="text-lg font-medium mb-4">Add Paperwork</h4>
                            <form action="{{ route('production_templates.paperwork.store', [$showCatalogue, $productionTemplate]) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="grid grid-cols-1 gap-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                                        <input name="title" class="input w-full" required />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                                        <select name="department" class="input w-full" required>
                                            <option value="">Select department...</option>
                                            @foreach($departments as $dept)
                                                <option value="{{ $dept->name }}">{{ $dept->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">File</label>
                                        <input type="file" name="file" class="input w-full" required />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                        <textarea name="notes" rows="3" class="input w-full"></textarea>
                                    </div>
                                    <div class="flex gap-2 mt-2">
                                        <button type="submit" class="btn">Save</button>
                                        <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'add-tpl-paperwork'}))">Cancel</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </x-modal>
                @endcan
            </div>

            {{-- ================================================================ --}}
            {{-- TAB 3: Baseline Notes --}}
            {{-- ================================================================ --}}
            <div id="tab-baseline-notes" class="tab-panel hidden">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-md font-semibold">Baseline Notes</h3>
                    @can('show_catalogues.update')
                        <button type="button" class="btn" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'add-tpl-note'}))">Add Note</button>
                    @endcan
                </div>

                @if(session('success') && request('tab') === 'baseline-notes')
                    <div class="mb-4 text-green-600">{{ session('success') }}</div>
                @endif

                <div class="bg-white shadow sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Note Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Note</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($productionTemplate->templateNotes as $note)
                                <tr>
                                    <td class="px-6 py-4 text-sm">{{ $note->note_type }}</td>
                                    <td class="px-6 py-4 text-sm">{{ $note->department ?? '—' }}</td>
                                    <td class="px-6 py-4 text-sm">{{ $note->author ?? '—' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        <button type="button" class="text-left text-blue-600" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'view-tn-{{ $note->id }}'}))">
                                            {{ Str::limit($note->note_text, 80) }}
                                        </button>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm">
                                        @can('show_catalogues.update')
                                            <button type="button" class="text-blue-600 mr-3" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'view-tn-{{ $note->id }}'}))">View</button>
                                            <button type="button" class="text-blue-600 mr-3" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'edit-tn-{{ $note->id }}'}))">Edit</button>
                                            <button type="button" class="text-red-600" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'delete-tn-{{ $note->id }}'}))">Delete</button>
                                        @endcan
                                    </td>
                                </tr>

                                {{-- View Note Modal --}}
                                <x-modal name="view-tn-{{ $note->id }}" focusable>
                                    <div class="p-6">
                                        <h4 class="text-lg font-medium mb-4">View Note</h4>
                                        <div class="grid grid-cols-1 gap-2 mb-4">
                                            <div><strong>Note Type:</strong> {{ $note->note_type }}</div>
                                            <div><strong>Department:</strong> {{ $note->department ?? '—' }}</div>
                                            <div><strong>Author:</strong> {{ $note->author ?? '—' }}</div>
                                            <div><strong>Note:</strong><div class="mt-1 text-gray-700 whitespace-pre-wrap">{{ $note->note_text }}</div></div>
                                        </div>
                                        <div class="flex gap-2">
                                            @can('show_catalogues.update')
                                                <button type="button" class="btn" onclick="(function(){window.dispatchEvent(new CustomEvent('close-modal',{detail:'view-tn-{{ $note->id }}'}));window.dispatchEvent(new CustomEvent('open-modal',{detail:'edit-tn-{{ $note->id }}'}));})();">Edit</button>
                                            @endcan
                                            <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'view-tn-{{ $note->id }}'}))">Close</button>
                                        </div>
                                    </div>
                                </x-modal>

                                {{-- Edit Note Modal --}}
                                <x-modal name="edit-tn-{{ $note->id }}" focusable>
                                    <div class="p-6">
                                        <h4 class="text-lg font-medium mb-4">Edit Note</h4>
                                        <form action="{{ route('production_templates.notes.update', [$showCatalogue, $productionTemplate, $note]) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="grid grid-cols-1 gap-3">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Note Type</label>
                                                    <input name="note_type" value="{{ $note->note_type }}" class="input w-full" required />
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                                                    <select name="department" class="input w-full">
                                                        <option value="">Select department...</option>
                                                        @foreach($departments as $dept)
                                                            <option value="{{ $dept->name }}" {{ ($note->department ?? '') === $dept->name ? 'selected' : '' }}>{{ $dept->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Author</label>
                                                    <input name="author" value="{{ $note->author }}" class="input w-full" />
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Note Text</label>
                                                    <textarea name="note_text" rows="5" class="input w-full" required>{{ $note->note_text }}</textarea>
                                                </div>
                                                <div class="flex gap-2 mt-2">
                                                    <button type="submit" class="btn">Save</button>
                                                    <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'edit-tn-{{ $note->id }}'}))">Cancel</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </x-modal>

                                {{-- Delete Note Modal --}}
                                <x-modal name="delete-tn-{{ $note->id }}" focusable>
                                    <div class="p-6">
                                        <h4 class="text-lg font-medium mb-4">Delete Note</h4>
                                        <p class="mb-4 text-gray-700">Are you sure you want to delete this <strong>{{ $note->note_type }}</strong> note?</p>
                                        <div class="flex gap-2">
                                            <form action="{{ route('production_templates.notes.destroy', [$showCatalogue, $productionTemplate, $note]) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-red-600">Yes, Delete</button>
                                            </form>
                                            <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'delete-tn-{{ $note->id }}'}))">Cancel</button>
                                        </div>
                                    </div>
                                </x-modal>
                            @empty
                                <tr><td colspan="5" class="px-6 py-4 text-gray-500">No notes.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Add Note Modal --}}
                @can('show_catalogues.update')
                    <x-modal name="add-tpl-note" focusable>
                        <div class="p-6">
                            <h4 class="text-lg font-medium mb-4">Add Note</h4>
                            <form action="{{ route('production_templates.notes.store', [$showCatalogue, $productionTemplate]) }}" method="POST">
                                @csrf
                                <div class="grid grid-cols-1 gap-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Note Type</label>
                                        <input name="note_type" class="input w-full" placeholder="e.g. Staging, Costume, Lighting" required />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                                        <select name="department" class="input w-full">
                                            <option value="">Select department...</option>
                                            @foreach($departments as $dept)
                                                <option value="{{ $dept->name }}">{{ $dept->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Author</label>
                                        <input name="author" class="input w-full" placeholder="Author name" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Note Text</label>
                                        <textarea name="note_text" rows="5" class="input w-full" required></textarea>
                                    </div>
                                    <div class="flex gap-2 mt-2">
                                        <button type="submit" class="btn">Save</button>
                                        <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'add-tpl-note'}))">Cancel</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </x-modal>
                @endcan
            </div>

            {{-- ================================================================ --}}
            {{-- TAB 4: Baseline Staffing --}}
            {{-- ================================================================ --}}
            <div id="tab-baseline-staffing" class="tab-panel hidden">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-md font-semibold">Baseline Staffing</h3>
                    @can('show_catalogues.update')
                        <button type="button" class="btn" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'add-tpl-staffing'}))">Add Staffing Line</button>
                    @endcan
                </div>

                @if(session('success') && request('tab') === 'baseline-staffing')
                    <div class="mb-4 text-green-600">{{ session('success') }}</div>
                @endif

                <div class="bg-white shadow sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($productionTemplate->staffing as $staff)
                                <tr>
                                    <td class="px-6 py-4 text-sm">{{ $staff->department }}</td>
                                    <td class="px-6 py-4 text-sm">{{ $staff->role }}</td>
                                    <td class="px-6 py-4 text-sm">{{ $staff->quantity }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ Str::limit($staff->notes, 60) }}</td>
                                    <td class="px-6 py-4 text-right text-sm">
                                        @can('show_catalogues.update')
                                            <button type="button" class="text-blue-600 mr-3" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'edit-ts-{{ $staff->id }}'}))">Edit</button>
                                            <button type="button" class="text-red-600" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'delete-ts-{{ $staff->id }}'}))">Delete</button>
                                        @endcan
                                    </td>
                                </tr>

                                {{-- Edit Staffing Modal --}}
                                <x-modal name="edit-ts-{{ $staff->id }}" focusable>
                                    <div class="p-6">
                                        <h4 class="text-lg font-medium mb-4">Edit Staffing Line</h4>
                                        <form action="{{ route('production_templates.staffing.update', [$showCatalogue, $productionTemplate, $staff]) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="grid grid-cols-1 gap-3">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                                                    <select name="department" class="input w-full" required>
                                                        <option value="">Select department...</option>
                                                        @foreach($departments as $dept)
                                                            <option value="{{ $dept->name }}" {{ $staff->department === $dept->name ? 'selected' : '' }}>{{ $dept->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                                                    <input name="role" value="{{ $staff->role }}" class="input w-full" required />
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                                                    <input type="number" name="quantity" value="{{ $staff->quantity }}" min="1" class="input w-full" required />
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                                    <textarea name="notes" rows="3" class="input w-full">{{ $staff->notes }}</textarea>
                                                </div>
                                                <div class="flex gap-2 mt-2">
                                                    <button type="submit" class="btn">Save</button>
                                                    <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'edit-ts-{{ $staff->id }}'}))">Cancel</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </x-modal>

                                {{-- Delete Staffing Modal --}}
                                <x-modal name="delete-ts-{{ $staff->id }}" focusable>
                                    <div class="p-6">
                                        <h4 class="text-lg font-medium mb-4">Delete Staffing Line</h4>
                                        <p class="mb-4 text-gray-700">Are you sure you want to delete <strong>{{ $staff->role }}</strong> in {{ $staff->department }}?</p>
                                        <div class="flex gap-2">
                                            <form action="{{ route('production_templates.staffing.destroy', [$showCatalogue, $productionTemplate, $staff]) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-red-600">Yes, Delete</button>
                                            </form>
                                            <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'delete-ts-{{ $staff->id }}'}))">Cancel</button>
                                        </div>
                                    </div>
                                </x-modal>
                            @empty
                                <tr><td colspan="5" class="px-6 py-4 text-gray-500">No staffing lines.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Add Staffing Modal --}}
                @can('show_catalogues.update')
                    <x-modal name="add-tpl-staffing" focusable>
                        <div class="p-6">
                            <h4 class="text-lg font-medium mb-4">Add Staffing Line</h4>
                            <form action="{{ route('production_templates.staffing.store', [$showCatalogue, $productionTemplate]) }}" method="POST">
                                @csrf
                                <div class="grid grid-cols-1 gap-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                                        <select name="department" class="input w-full" required>
                                            <option value="">Select department...</option>
                                            @foreach($departments as $dept)
                                                <option value="{{ $dept->name }}">{{ $dept->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                                        <input name="role" class="input w-full" placeholder="e.g. Stage Manager, Lighting Designer" required />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                                        <input type="number" name="quantity" value="1" min="1" class="input w-full" required />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                        <textarea name="notes" rows="3" class="input w-full"></textarea>
                                    </div>
                                    <div class="flex gap-2 mt-2">
                                        <button type="submit" class="btn">Save</button>
                                        <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'add-tpl-staffing'}))">Cancel</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </x-modal>
                @endcan
            </div>

            {{-- ================================================================ --}}
            {{-- TAB 5: Baseline Schedule Templates --}}
            {{-- ================================================================ --}}
            <div id="tab-baseline-schedules" class="tab-panel hidden">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-md font-semibold">Baseline Schedule Templates</h3>
                    @can('show_catalogues.update')
                        <button type="button" class="btn" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'add-tpl-schedule'}))">Add Schedule Template</button>
                    @endcan
                </div>

                @if(session('success') && request('tab') === 'baseline-schedules')
                    <div class="mb-4 text-green-600">{{ session('success') }}</div>
                @endif

                <div class="bg-white shadow sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Template Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($productionTemplate->schedules as $sched)
                                <tr>
                                    <td class="px-6 py-4 text-sm font-medium">{{ $sched->template_name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ Str::limit($sched->description, 80) }}</td>
                                    <td class="px-6 py-4 text-right text-sm">
                                        @can('show_catalogues.update')
                                            <button type="button" class="text-blue-600 mr-3" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'view-sched-{{ $sched->id }}'}))">View</button>
                                            <button type="button" class="text-blue-600 mr-3" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'edit-sched-{{ $sched->id }}'}))">Edit</button>
                                            <button type="button" class="text-red-600" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'delete-sched-{{ $sched->id }}'}))">Delete</button>
                                        @endcan
                                    </td>
                                </tr>

                                {{-- View Schedule Modal --}}
                                <x-modal name="view-sched-{{ $sched->id }}" focusable>
                                    <div class="p-6">
                                        <h4 class="text-lg font-medium mb-4">View Schedule Template</h4>
                                        <div class="grid grid-cols-1 gap-2 mb-4">
                                            <div><strong>Template Name:</strong> {{ $sched->template_name }}</div>
                                            <div><strong>Description:</strong><div class="mt-1 text-gray-700 whitespace-pre-wrap">{{ $sched->description ?? '—' }}</div></div>
                                            <div><strong>Schedule Data:</strong><div class="mt-1 text-gray-700 whitespace-pre-wrap border rounded p-3 bg-gray-50">{{ $sched->schedule_data ?? '—' }}</div></div>
                                        </div>
                                        <div class="flex gap-2">
                                            @can('show_catalogues.update')
                                                <button type="button" class="btn" onclick="(function(){window.dispatchEvent(new CustomEvent('close-modal',{detail:'view-sched-{{ $sched->id }}'}));window.dispatchEvent(new CustomEvent('open-modal',{detail:'edit-sched-{{ $sched->id }}'}));})();">Edit</button>
                                            @endcan
                                            <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'view-sched-{{ $sched->id }}'}))">Close</button>
                                        </div>
                                    </div>
                                </x-modal>

                                {{-- Edit Schedule Modal --}}
                                <x-modal name="edit-sched-{{ $sched->id }}" focusable>
                                    <div class="p-6">
                                        <h4 class="text-lg font-medium mb-4">Edit Schedule Template</h4>
                                        <form action="{{ route('production_templates.schedules.update', [$showCatalogue, $productionTemplate, $sched]) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="grid grid-cols-1 gap-3">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Template Name</label>
                                                    <input name="template_name" value="{{ $sched->template_name }}" class="input w-full" required />
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                                    <textarea name="description" rows="3" class="input w-full">{{ $sched->description }}</textarea>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Schedule Data</label>
                                                    <textarea name="schedule_data" rows="8" class="input w-full font-mono text-sm" placeholder="Enter schedule structure...">{{ $sched->schedule_data }}</textarea>
                                                </div>
                                                <div class="flex gap-2 mt-2">
                                                    <button type="submit" class="btn">Save</button>
                                                    <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'edit-sched-{{ $sched->id }}'}))">Cancel</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </x-modal>

                                {{-- Delete Schedule Modal --}}
                                <x-modal name="delete-sched-{{ $sched->id }}" focusable>
                                    <div class="p-6">
                                        <h4 class="text-lg font-medium mb-4">Delete Schedule Template</h4>
                                        <p class="mb-4 text-gray-700">Are you sure you want to delete <strong>{{ $sched->template_name }}</strong>?</p>
                                        <div class="flex gap-2">
                                            <form action="{{ route('production_templates.schedules.destroy', [$showCatalogue, $productionTemplate, $sched]) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-red-600">Yes, Delete</button>
                                            </form>
                                            <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'delete-sched-{{ $sched->id }}'}))">Cancel</button>
                                        </div>
                                    </div>
                                </x-modal>
                            @empty
                                <tr><td colspan="3" class="px-6 py-4 text-gray-500">No schedule templates.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Add Schedule Modal --}}
                @can('show_catalogues.update')
                    <x-modal name="add-tpl-schedule" focusable>
                        <div class="p-6">
                            <h4 class="text-lg font-medium mb-4">Add Schedule Template</h4>
                            <form action="{{ route('production_templates.schedules.store', [$showCatalogue, $productionTemplate]) }}" method="POST">
                                @csrf
                                <div class="grid grid-cols-1 gap-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Template Name</label>
                                        <input name="template_name" class="input w-full" required />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                        <textarea name="description" rows="3" class="input w-full"></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Schedule Data</label>
                                        <textarea name="schedule_data" rows="8" class="input w-full font-mono text-sm" placeholder="Enter schedule structure, e.g. day-by-day breakdown, rehearsal blocks, tech week layout..."></textarea>
                                    </div>
                                    <div class="flex gap-2 mt-2">
                                        <button type="submit" class="btn">Save</button>
                                        <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'add-tpl-schedule'}))">Cancel</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </x-modal>
                @endcan
            </div>

            {{-- ================================================================ --}}
            {{-- TAB 6: Files --}}
            {{-- ================================================================ --}}
            <div id="tab-files" class="tab-panel hidden">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-md font-semibold">Files</h3>
                    @can('show_catalogues.update')
                        <button type="button" class="btn" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'add-tpl-file'}))">Add File</button>
                    @endcan
                </div>

                @if(session('success') && request('tab') === 'files')
                    <div class="mb-4 text-green-600">{{ session('success') }}</div>
                @endif

                <div class="bg-white shadow sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($productionTemplate->files as $file)
                                <tr>
                                    <td class="px-6 py-4 text-sm">{{ $file->type }}</td>
                                    <td class="px-6 py-4 text-sm">{{ $file->title }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        <a href="{{ route('production_templates.files.download', [$showCatalogue, $productionTemplate, $file]) }}" class="text-blue-600">{{ $file->original_filename }}</a>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ Str::limit($file->notes, 60) }}</td>
                                    <td class="px-6 py-4 text-right text-sm">
                                        @can('show_catalogues.update')
                                            <button type="button" class="text-blue-600 mr-3" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'edit-tf-{{ $file->id }}'}))">Edit</button>
                                            <button type="button" class="text-red-600" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'delete-tf-{{ $file->id }}'}))">Delete</button>
                                        @endcan
                                    </td>
                                </tr>

                                {{-- Edit File Modal --}}
                                <x-modal name="edit-tf-{{ $file->id }}" focusable>
                                    <div class="p-6">
                                        <h4 class="text-lg font-medium mb-4">Edit File</h4>
                                        <form action="{{ route('production_templates.files.update', [$showCatalogue, $productionTemplate, $file]) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <div class="grid grid-cols-1 gap-3">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                                    <input name="type" value="{{ $file->type }}" class="input w-full" required />
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                                                    <input name="title" value="{{ $file->title }}" class="input w-full" required />
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Replace File (optional)</label>
                                                    <input type="file" name="file" class="input w-full" />
                                                    <p class="text-xs text-gray-500 mt-1">Current: {{ $file->original_filename }}</p>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                                    <textarea name="notes" rows="3" class="input w-full">{{ $file->notes }}</textarea>
                                                </div>
                                                <div class="flex gap-2 mt-2">
                                                    <button type="submit" class="btn">Save</button>
                                                    <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'edit-tf-{{ $file->id }}'}))">Cancel</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </x-modal>

                                {{-- Delete File Modal --}}
                                <x-modal name="delete-tf-{{ $file->id }}" focusable>
                                    <div class="p-6">
                                        <h4 class="text-lg font-medium mb-4">Delete File</h4>
                                        <p class="mb-4 text-gray-700">Are you sure you want to delete <strong>{{ $file->title }}</strong>?</p>
                                        <div class="flex gap-2">
                                            <form action="{{ route('production_templates.files.destroy', [$showCatalogue, $productionTemplate, $file]) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-red-600">Yes, Delete</button>
                                            </form>
                                            <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'delete-tf-{{ $file->id }}'}))">Cancel</button>
                                        </div>
                                    </div>
                                </x-modal>
                            @empty
                                <tr><td colspan="5" class="px-6 py-4 text-gray-500">No files.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Add File Modal --}}
                @can('show_catalogues.update')
                    <x-modal name="add-tpl-file" focusable>
                        <div class="p-6">
                            <h4 class="text-lg font-medium mb-4">Add File</h4>
                            <form action="{{ route('production_templates.files.store', [$showCatalogue, $productionTemplate]) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="grid grid-cols-1 gap-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                        <input name="type" class="input w-full" placeholder="e.g. Score, Script, Design" required />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                                        <input name="title" class="input w-full" required />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">File</label>
                                        <input type="file" name="file" class="input w-full" required />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                        <textarea name="notes" rows="3" class="input w-full"></textarea>
                                    </div>
                                    <div class="flex gap-2 mt-2">
                                        <button type="submit" class="btn">Save</button>
                                        <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'add-tpl-file'}))">Cancel</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </x-modal>
                @endcan
            </div>

        </div> {{-- end p-6 --}}
    </div> {{-- end bg-white card --}}
</div>

{{-- Edit Template Modal --}}
@can('show_catalogues.update')
    <x-modal name="edit-template" focusable>
        <div class="p-6">
            <h4 class="text-lg font-medium mb-4">Edit Production Template</h4>
            <form action="{{ route('show_catalogues.production_templates.update', [$showCatalogue, $productionTemplate]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input name="name" value="{{ $productionTemplate->name }}" class="input w-full" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="3" class="input w-full">{{ $productionTemplate->description }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea name="notes" rows="3" class="input w-full">{{ $productionTemplate->notes }}</textarea>
                    </div>
                    <div class="flex gap-2 mt-2">
                        <button type="submit" class="btn">Save</button>
                        <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'edit-template'}))">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal name="delete-template" focusable>
        <div class="p-6">
            <h4 class="text-lg font-medium mb-4">Delete Production Template</h4>
            <p class="mb-4 text-gray-700">Are you sure you want to delete <strong>{{ $productionTemplate->name }}</strong>? This cannot be undone.</p>
            <div class="flex gap-2">
                <form action="{{ route('show_catalogues.production_templates.destroy', [$showCatalogue, $productionTemplate]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button class="text-red-600">Yes, Delete</button>
                </form>
                <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'delete-template'}))">Cancel</button>
            </div>
        </div>
    </x-modal>
@endcan

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

        document.addEventListener('DOMContentLoaded', function(){
            var params = new URLSearchParams(window.location.search);
            var tab = params.get('tab') || 'overview';
            activateTab(tab);
        });
    })();
</script>
@endsection
