<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ $season->name }}</h2>
            <div>
                <a href="{{ route('seasons.edit', $season) }}" class="text-blue-600">Edit</a>
                <a href="{{ route('seasons.index') }}" class="ml-4 text-gray-600">Back</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-4">
                        <strong>Start:</strong> {{ optional($season->start_date)->toDateString() ?? '—' }}
                    </div>
                    <div class="mb-4">
                        <strong>End:</strong> {{ optional($season->end_date)->toDateString() ?? '—' }}
                    </div>
                    <div>
                        <strong>Notes</strong>
                        <div class="mt-2 text-gray-700">{{ $season->notes ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
