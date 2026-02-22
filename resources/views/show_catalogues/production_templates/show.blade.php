@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="font-semibold text-xl text-gray-800">{{ $productionTemplate->name }}</h2>
        <div>
            <a href="{{ route('show_catalogues.show', $showCatalogue) }}?tab=production-catalogue" class="text-gray-600">Back to Catalogue</a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 text-green-600">{{ session('success') }}</div>
    @endif

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="mb-4">
                <strong>Show Catalogue:</strong>
                <a href="{{ route('show_catalogues.show', $showCatalogue) }}" class="text-blue-600">{{ $showCatalogue->title }}</a>
            </div>
            <div class="mb-4">
                <strong>Name:</strong> {{ $productionTemplate->name }}
            </div>
            <div class="mb-4">
                <strong>Description:</strong>
                <div class="mt-1 text-gray-700 whitespace-pre-wrap">{{ $productionTemplate->description ?? '—' }}</div>
            </div>
            <div class="mb-4">
                <strong>Notes:</strong>
                <div class="mt-1 text-gray-700 whitespace-pre-wrap">{{ $productionTemplate->notes ?? '—' }}</div>
            </div>
            <div class="mb-4">
                <strong>Created:</strong> {{ $productionTemplate->created_at->toDateString() }}
            </div>
            <div class="mb-4">
                <strong>Last Updated:</strong> {{ $productionTemplate->updated_at->toDateString() }}
            </div>

            @can('show_catalogues.update')
                <div class="flex gap-3 mt-6">
                    <button type="button" class="btn" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'edit-template'}))">Edit Template</button>
                    <button type="button" class="text-red-600" onclick="window.dispatchEvent(new CustomEvent('open-modal',{detail:'delete-template'}))">Delete Template</button>
                </div>
            @endcan
        </div>
    </div>
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
@endsection
