<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Seasons') }}</h2>
            <a href="{{ route('seasons.create') }}" class="btn">Create Season</a>
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
                                <th class="px-2 py-2">Name</th>
                                <th class="px-2 py-2">Start</th>
                                <th class="px-2 py-2">End</th>
                                <th class="px-2 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($seasons as $season)
                                <tr class="border-t">
                                    <td class="px-2 py-2"> <a href="{{ route('seasons.show', $season) }}" class="text-blue-600">{{ $season->name }}</a> </td>
                                    <td class="px-2 py-2">{{ optional($season->start_date)->toDateString() }}</td>
                                    <td class="px-2 py-2">{{ optional($season->end_date)->toDateString() }}</td>
                                    <td class="px-2 py-2">
                                        <a href="{{ route('seasons.edit', $season) }}" class="text-sm text-blue-600">Edit</a>
                                        <form action="{{ route('seasons.destroy', $season) }}" method="POST" class="inline ml-2">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-sm text-red-600">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-2 py-4" colspan="4">No seasons.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
