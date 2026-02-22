@extends('layouts.app')

@php
use Illuminate\Support\Str;
@endphp

@section('content')
<div class="max-w-5xl mx-auto py-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="font-semibold text-xl text-gray-800">{{ $showCatalogue->title }}</h2>
        <div>
            @can('show_catalogues.update')
                <a href="{{ route('show_catalogues.edit', $showCatalogue) }}" class="text-blue-600">Edit</a>
            @endcan
            <a href="{{ route('show_catalogues.index') }}" class="ml-4 text-gray-600">Back</a>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">

            {{-- ===== Tab Navigation ===== --}}
            <div class="mb-4 border-b">
                <nav class="-mb-px flex space-x-6 overflow-x-auto" aria-label="Tabs">
                    <button data-tab="overview" class="tab-btn pb-2 text-sm font-medium text-gray-700 border-b-2 border-transparent whitespace-nowrap">Overview</button>
                    <button data-tab="baseline-paperwork" class="tab-btn pb-2 text-sm font-medium text-gray-700 border-b-2 border-transparent whitespace-nowrap">Baseline Paperwork</button>
                    <button data-tab="baseline-notes" class="tab-btn pb-2 text-sm font-medium text-gray-700 border-b-2 border-transparent whitespace-nowrap">Baseline Notes</button>
                    <button data-tab="baseline-contacts" class="tab-btn pb-2 text-sm font-medium text-gray-700 border-b-2 border-transparent whitespace-nowrap">Baseline Contacts</button>
                    <button data-tab="files" class="tab-btn pb-2 text-sm font-medium text-gray-700 border-b-2 border-transparent whitespace-nowrap">Files</button>
                    <button data-tab="production-catalogue" class="tab-btn pb-2 text-sm font-medium text-gray-700 border-b-2 border-transparent whitespace-nowrap">Production Catalogue</button>
                </nav>
            </div>

            {{-- ================================================================ --}}
            {{-- TAB 1: Overview --}}
            {{-- ================================================================ --}}
            <div id="tab-overview" class="tab-panel">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="mb-4"><strong>Title:</strong> {{ $showCatalogue->title }}</div>
                        <div class="mb-4"><strong>Choreography By:</strong> {{ $showCatalogue->choreography_by ?? '—' }}</div>
                        <div class="mb-4"><strong>Version:</strong> {{ $showCatalogue->version ?? '—' }}</div>
                        <div class="mb-4"><strong>Created:</strong> {{ $showCatalogue->created_at->toDateString() }}</div>
                        <div class="mb-4"><strong>Rights / Licensing:</strong>
                            <div class="mt-1 text-gray-700 whitespace-pre-wrap">{{ $showCatalogue->rights_licensing ?? '—' }}</div>
                        </div>
                        <div class="mb-4"><strong>Tags:</strong>
                            @if(!empty($showCatalogue->tags))
                                @foreach($showCatalogue->tags as $tag)
                                    <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-0.5 rounded mr-1">{{ $tag }}</span>
                                @endforeach
                            @else
                                <span class="text-gray-500">—</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <div class="mb-4"><strong>Thumbnail:</strong></div>
                        @if($showCatalogue->thumbnail)
                            <img src="{{ asset('storage/' . $showCatalogue->thumbnail) }}" alt="Thumbnail" class="max-w-xs rounded shadow" />
                        @else
                            <span class="text-gray-500">No thumbnail uploaded.</span>
                        @endif
                    </div>
                </div>
                <div class="mt-4">
                    <strong>Description:</strong>
                    <div class="mt-2 text-gray-700 whitespace-pre-wrap">{{ $showCatalogue->description ?? '—' }}</div>
                </div>
            </div>

            {{-- ================================================================ --}}
            {{-- TAB 2: Baseline Paperwork --}}
            {{-- ================================================================ --}}
            <div id="tab-baseline-paperwork" class="tab-panel hidden">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-md font-semibold">Baseline Paperwork</h3>
                    @can('show_catalogues.update')
                        <button type="button" class="btn" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'add-paperwork'}))">Add Paperwork</button>
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
                            @forelse($showCatalogue->paperwork as $pw)
                                <tr>
                                    <td class="px-6 py-4 text-sm">{{ $pw->title }}</td>
                                    <td class="px-6 py-4 text-sm">{{ $pw->department }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        <a href="{{ route('show_catalogues.paperwork.download', [$showCatalogue, $pw]) }}" class="text-blue-600">{{ $pw->original_filename }}</a>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ Str::limit($pw->notes, 60) }}</td>
                                    <td class="px-6 py-4 text-right text-sm">
                                        @can('show_catalogues.update')
                                            <button type="button" class="text-blue-600 mr-3" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'edit-paperwork-{{ $pw->id }}'}))">Edit</button>
                                            <button type="button" class="text-red-600" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'delete-paperwork-{{ $pw->id }}'}))">Delete</button>
                                        @endcan
                                    </td>
                                </tr>

                                {{-- Edit Paperwork Modal --}}
                                <x-modal name="edit-paperwork-{{ $pw->id }}" focusable>
                                    <div class="p-6">
                                        <h4 class="text-lg font-medium mb-4">Edit Paperwork</h4>
                                        <form action="{{ route('show_catalogues.paperwork.update', [$showCatalogue, $pw]) }}" method="POST" enctype="multipart/form-data">
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
                                                    <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'edit-paperwork-{{ $pw->id }}'}))">Cancel</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </x-modal>

                                {{-- Delete Paperwork Modal --}}
                                <x-modal name="delete-paperwork-{{ $pw->id }}" focusable>
                                    <div class="p-6">
                                        <h4 class="text-lg font-medium mb-4">Delete Paperwork</h4>
                                        <p class="mb-4 text-gray-700">Are you sure you want to delete <strong>{{ $pw->title }}</strong>?</p>
                                        <div class="flex gap-2">
                                            <form action="{{ route('show_catalogues.paperwork.destroy', [$showCatalogue, $pw]) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-red-600">Yes, Delete</button>
                                            </form>
                                            <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'delete-paperwork-{{ $pw->id }}'}))">Cancel</button>
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
                    <x-modal name="add-paperwork" focusable>
                        <div class="p-6">
                            <h4 class="text-lg font-medium mb-4">Add Paperwork</h4>
                            <form action="{{ route('show_catalogues.paperwork.store', $showCatalogue) }}" method="POST" enctype="multipart/form-data">
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
                                        <button type="submit" class="btn">Add</button>
                                        <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'add-paperwork'}))">Cancel</button>
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
                        <button type="button" class="btn" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'add-note'}))">Add Note</button>
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
                            @forelse($showCatalogue->notes as $note)
                                <tr>
                                    <td class="px-6 py-4 text-sm">{{ $note->note_type }}</td>
                                    <td class="px-6 py-4 text-sm">{{ $note->department ?? '—' }}</td>
                                    <td class="px-6 py-4 text-sm">{{ $note->author ?? '—' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        <button type="button" class="text-left text-blue-600" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'view-note-{{ $note->id }}'}))">
                                            {{ Str::limit($note->note_text, 80) }}
                                        </button>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm">
                                        @can('show_catalogues.update')
                                            <button type="button" class="text-blue-600 mr-3" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'edit-note-{{ $note->id }}'}))">Edit</button>
                                            <button type="button" class="text-red-600" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'delete-note-{{ $note->id }}'}))">Delete</button>
                                        @endcan
                                    </td>
                                </tr>

                                {{-- View Note Modal --}}
                                <x-modal name="view-note-{{ $note->id }}" focusable>
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
                                                <button type="button" class="btn" onclick="(function(){window.dispatchEvent(new CustomEvent('close-modal',{detail:'view-note-{{ $note->id }}'}));window.dispatchEvent(new CustomEvent('open-modal',{detail:'edit-note-{{ $note->id }}'}));})();">Edit</button>
                                            @endcan
                                            <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'view-note-{{ $note->id }}'}))">Close</button>
                                        </div>
                                    </div>
                                </x-modal>

                                {{-- Edit Note Modal --}}
                                <x-modal name="edit-note-{{ $note->id }}" focusable>
                                    <div class="p-6">
                                        <h4 class="text-lg font-medium mb-4">Edit Note</h4>
                                        <form action="{{ route('show_catalogues.notes.update', [$showCatalogue, $note]) }}" method="POST">
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
                                                    <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'edit-note-{{ $note->id }}'}))">Cancel</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </x-modal>

                                {{-- Delete Note Modal --}}
                                <x-modal name="delete-note-{{ $note->id }}" focusable>
                                    <div class="p-6">
                                        <h4 class="text-lg font-medium mb-4">Delete Note</h4>
                                        <p class="mb-4 text-gray-700">Are you sure you want to delete this <strong>{{ $note->note_type }}</strong> note?</p>
                                        <div class="flex gap-2">
                                            <form action="{{ route('show_catalogues.notes.destroy', [$showCatalogue, $note]) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-red-600">Yes, Delete</button>
                                            </form>
                                            <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'delete-note-{{ $note->id }}'}))">Cancel</button>
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
                    <x-modal name="add-note" focusable>
                        <div class="p-6">
                            <h4 class="text-lg font-medium mb-4">Add Note</h4>
                            <form action="{{ route('show_catalogues.notes.store', $showCatalogue) }}" method="POST">
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
                                        <button type="submit" class="btn">Add</button>
                                        <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'add-note'}))">Cancel</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </x-modal>
                @endcan
            </div>

            {{-- ================================================================ --}}
            {{-- TAB 4: Baseline Contacts --}}
            {{-- ================================================================ --}}
            <div id="tab-baseline-contacts" class="tab-panel hidden">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-md font-semibold">Baseline Contacts</h3>
                    @can('show_catalogues.update')
                        <button type="button" class="btn" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'add-catalogue-contact'}))">Add Contact</button>
                    @endcan
                </div>

                @if(session('success') && request('tab') === 'baseline-contacts')
                    <div class="mb-4 text-green-600">{{ session('success') }}</div>
                @endif

                <div class="bg-white shadow sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($showCatalogue->catalogueContacts as $cc)
                                <tr>
                                    <td class="px-6 py-4 text-sm">
                                        @if($cc->contact)
                                            <a href="{{ route('contacts.show', $cc->contact) }}" class="text-blue-600">{{ $cc->contact->first_name }} {{ $cc->contact->last_name }}</a>
                                        @else
                                            <span class="text-gray-500">Unknown</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm">{{ $cc->role }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ Str::limit($cc->notes, 60) }}</td>
                                    <td class="px-6 py-4 text-right text-sm">
                                        @can('show_catalogues.update')
                                            <button type="button" class="text-blue-600 mr-3" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'edit-cc-{{ $cc->id }}'}))">Edit</button>
                                            <button type="button" class="text-red-600" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'delete-cc-{{ $cc->id }}'}))">Delete</button>
                                        @endcan
                                    </td>
                                </tr>

                                {{-- Edit Contact Modal --}}
                                <x-modal name="edit-cc-{{ $cc->id }}" focusable>
                                    <div class="p-6">
                                        <h4 class="text-lg font-medium mb-4">Edit Contact Association</h4>
                                        <form action="{{ route('show_catalogues.catalogue_contacts.update', [$showCatalogue, $cc]) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="grid grid-cols-1 gap-3">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Contact</label>
                                                    <select name="contact_id" class="input w-full" required>
                                                        <option value="">Select contact...</option>
                                                        @foreach($contacts as $c)
                                                            <option value="{{ $c->id }}" {{ $cc->contact_id == $c->id ? 'selected' : '' }}>{{ $c->last_name }}, {{ $c->first_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                                                    <input name="role" value="{{ $cc->role }}" class="input w-full" required />
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                                    <textarea name="notes" rows="3" class="input w-full">{{ $cc->notes }}</textarea>
                                                </div>
                                                <div class="flex gap-2 mt-2">
                                                    <button type="submit" class="btn">Save</button>
                                                    <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'edit-cc-{{ $cc->id }}'}))">Cancel</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </x-modal>

                                {{-- Delete Contact Modal --}}
                                <x-modal name="delete-cc-{{ $cc->id }}" focusable>
                                    <div class="p-6">
                                        <h4 class="text-lg font-medium mb-4">Remove Contact</h4>
                                        <p class="mb-4 text-gray-700">Are you sure you want to remove <strong>{{ $cc->contact ? $cc->contact->first_name . ' ' . $cc->contact->last_name : 'this contact' }}</strong> from this catalogue entry?</p>
                                        <div class="flex gap-2">
                                            <form action="{{ route('show_catalogues.catalogue_contacts.destroy', [$showCatalogue, $cc]) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-red-600">Yes, Remove</button>
                                            </form>
                                            <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'delete-cc-{{ $cc->id }}'}))">Cancel</button>
                                        </div>
                                    </div>
                                </x-modal>
                            @empty
                                <tr><td colspan="4" class="px-6 py-4 text-gray-500">No contacts associated.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Add Contact Modal --}}
                @can('show_catalogues.update')
                    <x-modal name="add-catalogue-contact" focusable>
                        <div class="p-6">
                            <h4 class="text-lg font-medium mb-4">Add Contact</h4>
                            <form action="{{ route('show_catalogues.catalogue_contacts.store', $showCatalogue) }}" method="POST">
                                @csrf
                                <div class="grid grid-cols-1 gap-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact</label>
                                        <select name="contact_id" class="input w-full" required>
                                            <option value="">Select contact...</option>
                                            @foreach($contacts as $c)
                                                <option value="{{ $c->id }}">{{ $c->last_name }}, {{ $c->first_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                                        <input name="role" class="input w-full" placeholder="e.g. Choreographer, Director, Composer" required />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                        <textarea name="notes" rows="3" class="input w-full"></textarea>
                                    </div>
                                    <div class="flex gap-2 mt-2">
                                        <button type="submit" class="btn">Add</button>
                                        <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'add-catalogue-contact'}))">Cancel</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </x-modal>
                @endcan
            </div>

            {{-- ================================================================ --}}
            {{-- TAB 5: Files --}}
            {{-- ================================================================ --}}
            <div id="tab-files" class="tab-panel hidden">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-md font-semibold">Files</h3>
                    @can('show_catalogues.update')
                        <button type="button" class="btn" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'add-catalogue-file'}))">Add File</button>
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
                            @forelse($showCatalogue->files as $file)
                                <tr>
                                    <td class="px-6 py-4 text-sm">{{ $file->type }}</td>
                                    <td class="px-6 py-4 text-sm">{{ $file->title }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        <a href="{{ route('show_catalogues.catalogue_files.download', [$showCatalogue, $file]) }}" class="text-blue-600">{{ $file->original_filename }}</a>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ Str::limit($file->notes, 60) }}</td>
                                    <td class="px-6 py-4 text-right text-sm">
                                        @can('show_catalogues.update')
                                            <button type="button" class="text-blue-600 mr-3" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'edit-file-{{ $file->id }}'}))">Edit</button>
                                            <button type="button" class="text-red-600" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'delete-file-{{ $file->id }}'}))">Delete</button>
                                        @endcan
                                    </td>
                                </tr>

                                {{-- Edit File Modal --}}
                                <x-modal name="edit-file-{{ $file->id }}" focusable>
                                    <div class="p-6">
                                        <h4 class="text-lg font-medium mb-4">Edit File</h4>
                                        <form action="{{ route('show_catalogues.catalogue_files.update', [$showCatalogue, $file]) }}" method="POST" enctype="multipart/form-data">
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
                                                    <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'edit-file-{{ $file->id }}'}))">Cancel</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </x-modal>

                                {{-- Delete File Modal --}}
                                <x-modal name="delete-file-{{ $file->id }}" focusable>
                                    <div class="p-6">
                                        <h4 class="text-lg font-medium mb-4">Delete File</h4>
                                        <p class="mb-4 text-gray-700">Are you sure you want to delete <strong>{{ $file->title }}</strong>?</p>
                                        <div class="flex gap-2">
                                            <form action="{{ route('show_catalogues.catalogue_files.destroy', [$showCatalogue, $file]) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-red-600">Yes, Delete</button>
                                            </form>
                                            <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'delete-file-{{ $file->id }}'}))">Cancel</button>
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
                    <x-modal name="add-catalogue-file" focusable>
                        <div class="p-6">
                            <h4 class="text-lg font-medium mb-4">Add File</h4>
                            <form action="{{ route('show_catalogues.catalogue_files.store', $showCatalogue) }}" method="POST" enctype="multipart/form-data">
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
                                        <button type="submit" class="btn">Add</button>
                                        <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'add-catalogue-file'}))">Cancel</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </x-modal>
                @endcan
            </div>

            {{-- ================================================================ --}}
            {{-- TAB 6: Production Catalogue --}}
            {{-- ================================================================ --}}
            <div id="tab-production-catalogue" class="tab-panel hidden">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-md font-semibold">Production Catalogue</h3>
                    @can('show_catalogues.update')
                        <button type="button" class="btn" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'add-production-template'}))">Add Production Template</button>
                    @endcan
                </div>

                @if(session('success') && request('tab') === 'production-catalogue')
                    <div class="mb-4 text-green-600">{{ session('success') }}</div>
                @endif

                <div class="bg-white shadow sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Updated</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($showCatalogue->productionTemplates as $tpl)
                                <tr>
                                    <td class="px-6 py-4 text-sm">
                                        <a href="{{ route('show_catalogues.production_templates.show', [$showCatalogue, $tpl]) }}" class="text-blue-600 font-medium">{{ $tpl->name }}</a>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ Str::limit($tpl->description, 60) }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $tpl->updated_at->toDateString() }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ Str::limit($tpl->notes, 40) }}</td>
                                    <td class="px-6 py-4 text-right text-sm">
                                        @can('show_catalogues.update')
                                            <button type="button" class="text-blue-600 mr-3" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'edit-tpl-{{ $tpl->id }}'}))">Edit</button>
                                            <button type="button" class="text-red-600" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'delete-tpl-{{ $tpl->id }}'}))">Delete</button>
                                        @endcan
                                    </td>
                                </tr>

                                {{-- Edit Template Modal --}}
                                <x-modal name="edit-tpl-{{ $tpl->id }}" focusable>
                                    <div class="p-6">
                                        <h4 class="text-lg font-medium mb-4">Edit Production Template</h4>
                                        <form action="{{ route('show_catalogues.production_templates.update', [$showCatalogue, $tpl]) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="grid grid-cols-1 gap-3">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                                    <input name="name" value="{{ $tpl->name }}" class="input w-full" required />
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                                    <textarea name="description" rows="3" class="input w-full">{{ $tpl->description }}</textarea>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                                    <textarea name="notes" rows="3" class="input w-full">{{ $tpl->notes }}</textarea>
                                                </div>
                                                <div class="flex gap-2 mt-2">
                                                    <button type="submit" class="btn">Save</button>
                                                    <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'edit-tpl-{{ $tpl->id }}'}))">Cancel</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </x-modal>

                                {{-- Delete Template Modal --}}
                                <x-modal name="delete-tpl-{{ $tpl->id }}" focusable>
                                    <div class="p-6">
                                        <h4 class="text-lg font-medium mb-4">Delete Production Template</h4>
                                        <p class="mb-4 text-gray-700">Are you sure you want to delete <strong>{{ $tpl->name }}</strong>?</p>
                                        <div class="flex gap-2">
                                            <form action="{{ route('show_catalogues.production_templates.destroy', [$showCatalogue, $tpl]) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-red-600">Yes, Delete</button>
                                            </form>
                                            <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'delete-tpl-{{ $tpl->id }}'}))">Cancel</button>
                                        </div>
                                    </div>
                                </x-modal>
                            @empty
                                <tr><td colspan="5" class="px-6 py-4 text-gray-500">No production templates.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Add Production Template Modal --}}
                @can('show_catalogues.update')
                    <x-modal name="add-production-template" focusable>
                        <div class="p-6">
                            <h4 class="text-lg font-medium mb-4">Add Production Template</h4>
                            <form action="{{ route('show_catalogues.production_templates.store', $showCatalogue) }}" method="POST">
                                @csrf
                                <div class="grid grid-cols-1 gap-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                        <input name="name" class="input w-full" required />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                        <textarea name="description" rows="3" class="input w-full"></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                        <textarea name="notes" rows="3" class="input w-full"></textarea>
                                    </div>
                                    <div class="flex gap-2 mt-2">
                                        <button type="submit" class="btn">Save</button>
                                        <button type="button" class="ml-2 text-gray-600" onclick="window.dispatchEvent(new CustomEvent('close-modal',{detail:'add-production-template'}))">Cancel</button>
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
