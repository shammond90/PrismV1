<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('New Catalogue Entry') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @can('show_catalogues.create')
                        <form action="{{ route('show_catalogues.store') }}" method="POST">
                            @csrf
                            <div class="grid gap-4">
                                <label class="block"><span class="text-sm text-gray-700">Title</span><input name="title" value="{{ old('title') }}" class="input mt-1 block w-full" /></label>
                                <label class="block"><span class="text-sm text-gray-700">Choreography By</span><input type="text" name="choreography_by" class="input mt-1 block w-full" value="{{ old('choreography_by') }}" /></label>
                                <label class="block"><span class="text-sm text-gray-700">Version</span><input name="version" value="{{ old('version') }}" class="input mt-1 block w-full" /></label>
                                <label class="block"><span class="text-sm text-gray-700">Description</span><textarea name="description" class="input mt-1 block w-full">{{ old('description') }}</textarea></label>
                                <label class="block"><span class="text-sm text-gray-700">Creation Date</span><input type="date" name="created_at" value="{{ old('created_at') }}" class="input mt-1 block w-full" /></label>
                                <div class="flex gap-2"><button class="btn">Create</button><a href="{{ route('show_catalogues.index') }}" class="ml-2 text-gray-600">Cancel</a></div>
                            </div>
                        </form>
                    @else
                        <div class="text-red-600">You do not have permission to create catalogue entries.</div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
