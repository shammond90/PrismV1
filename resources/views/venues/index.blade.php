<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Venues') }}</h2>
            @can('venues.create')
                <a href="{{ route('venues.create') }}" class="btn">New Venue</a>
            @endcan
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
                                <th class="px-2 py-2">Type</th>
                                <th class="px-2 py-2">Website</th>
                                <th class="px-2 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($venues as $venue)
                                <tr class="border-t">
                                    <td class="px-2 py-2"><a href="{{ route('venues.show', $venue) }}" class="text-blue-600">{{ $venue->name }}</a></td>
                                    <td class="px-2 py-2">{{ $venue->type }}</td>
                                    <td class="px-2 py-2"><a href="{{ $venue->website }}" class="text-blue-600">{{ $venue->website }}</a></td>
                                    <td class="px-2 py-2">
                                        @can('venues.update')
                                            <a href="{{ route('venues.edit', $venue) }}" class="ml-2 text-blue-600">Edit</a>
                                        @endcan
                                        @can('venues.delete')
                                            <form action="{{ route('venues.destroy', $venue) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Delete venue?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600">Delete</button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-2 py-4" colspan="4">No venues found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">{{ $venues->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
