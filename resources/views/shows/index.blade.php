<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Shows') }}</h2>
            @can('shows.create')
                <a href="{{ route('shows.create') }}" class="btn">Create Show</a>
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
                                <th class="px-2 py-2">Title</th>
                                <th class="px-2 py-2">Season</th>
                                <th class="px-2 py-2">Opening</th>
                                <th class="px-2 py-2">Status</th>
                                <th class="px-2 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($shows as $show)
                                <tr class="border-t">
                                    <td class="px-2 py-2">
                                        <a href="{{ route('shows.show', $show) }}" class="text-blue-600">
                                            {{ $show->title }}@if(!empty($show->choreography_by)) By {{ $show->choreography_by }}@endif
                                        </a>
                                    </td>
                                    <td class="px-2 py-2">{{ $show->season->name }}</td>
                                    <td class="px-2 py-2">{{ optional($show->opening_date)->toDateString() }}</td>
                                    <td class="px-2 py-2">{{ $show->status }}</td>
                                    <td class="px-2 py-2">
                                        @can('shows.update')
                                            <a href="{{ route('shows.edit', $show) }}" class="text-sm text-blue-600">Edit</a>
                                        @endcan
                                        @can('shows.delete')
                                            <form action="{{ route('shows.destroy', $show) }}" method="POST" class="inline ml-2">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-sm text-red-600">Delete</button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-2 py-4" colspan="6">No shows.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
