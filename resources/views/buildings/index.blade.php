<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Buildings') }}</h2>
            @can('buildings.create')
                <a href="{{ route('buildings.create') }}" class="btn">New Building</a>
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
                                <th class="px-2 py-2">Venue</th>
                                <th class="px-2 py-2">Website</th>
                                <th class="px-2 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($buildings as $building)
                                <tr class="border-t">
                                    <td class="px-2 py-2"><a href="{{ route('buildings.show', $building) }}" class="text-blue-600">{{ $building->name }}</a></td>
                                    <td class="px-2 py-2">@if($building->venue)<a href="{{ route('venues.show', $building->venue) }}" class="text-blue-600">{{ $building->venue->name }}</a>@else<span class="text-gray-500">No venue</span>@endif</td>
                                    <td class="px-2 py-2"><a href="{{ $building->website }}" class="text-blue-600">{{ $building->website }}</a></td>
                                    <td class="px-2 py-2">
                                        @can('buildings.update')
                                            <a href="{{ route('buildings.edit', $building) }}" class="ml-2 text-blue-600">Edit</a>
                                        @endcan
                                        @can('buildings.delete')
                                            <form action="{{ route('buildings.destroy', $building) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Delete building?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600">Delete</button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-2 py-4" colspan="4">No buildings found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">{{ $buildings->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
