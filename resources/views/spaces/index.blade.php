<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Spaces') }}</h2>
            @can('spaces.create')
                <a href="{{ route('spaces.create') }}" class="btn">New Space</a>
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
                                <th class="px-2 py-2">Building</th>
                                <th class="px-2 py-2">Type</th>
                                <th class="px-2 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($spaces as $space)
                                <tr class="border-t">
                                    <td class="px-2 py-2"><a href="{{ route('spaces.show', $space) }}" class="text-blue-600">{{ $space->name }}</a></td>
                                    <td class="px-2 py-2">@if($space->building)<a href="{{ route('buildings.show', $space->building) }}" class="text-blue-600">{{ $space->building->name }}</a>@else<span class="text-gray-500">No building</span>@endif</td>
                                    <td class="px-2 py-2">{{ $space->type }}</td>
                                    <td class="px-2 py-2">
                                        @can('spaces.update')
                                            <a href="{{ route('spaces.edit', $space) }}" class="ml-2 text-blue-600">Edit</a>
                                        @endcan
                                        @can('spaces.delete')
                                            <form action="{{ route('spaces.destroy', $space) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Delete space?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600">Delete</button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-2 py-4" colspan="4">No spaces found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">{{ $spaces->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
