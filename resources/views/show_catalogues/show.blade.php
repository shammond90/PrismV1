<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ $showCatalogue->title }}</h2>
            <div>
                @can('show_catalogues.update')
                    <a href="{{ route('show_catalogues.edit', $showCatalogue) }}" class="text-blue-600">Edit</a>
                @endcan
                <a href="{{ route('show_catalogues.index') }}" class="ml-4 text-gray-600">Back</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-4"><strong>Choreography By:</strong> {{ $showCatalogue->choreography_by ?? '—' }}</div>                  
                    <div class="mb-4"><strong>Version:</strong> {{ $showCatalogue->version ?? '—' }}</div>
                    <div class="mb-4"><strong>Created:</strong> {{ $showCatalogue->created_at->toDateString() }}</div>
                    <div><strong>Description</strong><div class="mt-2 text-gray-700">{{ $showCatalogue->description ?? '—' }}</div></div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
