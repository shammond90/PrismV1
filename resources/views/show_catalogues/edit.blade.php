<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Edit Catalogue Entry') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @can('show_catalogues.update')
                        <form action="{{ route('show_catalogues.update', $showCatalogue) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="grid gap-4">
                                <label class="block"><span class="text-sm text-gray-700">Title</span><input name="title" value="{{ old('title', $showCatalogue->title) }}" class="input mt-1 block w-full" /></label>
                                <label class="block"><span class="text-sm text-gray-700">Choreography By</span><input type="text" name="choreography_by" value="{{ old('choreography_by', $showCatalogue->choreography_by) }}" class="input mt-1 block w-full" /></label>
                                <label class="block"><span class="text-sm text-gray-700">Version</span><input name="version" value="{{ old('version', $showCatalogue->version) }}" class="input mt-1 block w-full" /></label>
                                <label class="block"><span class="text-sm text-gray-700">Description</span><textarea name="description" class="input mt-1 block w-full">{{ old('description', $showCatalogue->description) }}</textarea></label>
                                <label class="block"><span class="text-sm text-gray-700">Creation Date</span><input type="date" name="created_at" value="{{ old('created_at', optional($showCatalogue->created_at)->toDateString()) }}" class="input mt-1 block w-full" /></label>
                                <div class="flex gap-2"><button class="btn">Save</button><a href="{{ route('show_catalogues.show', $showCatalogue) }}" class="ml-2 text-gray-600">Cancel</a></div>
                            </div>
                        </form>
                    @else
                        <div class="text-red-600">You do not have permission to edit this entry.</div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
