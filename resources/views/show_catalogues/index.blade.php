<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Show Catalogue') }}</h2>
            @can('show_catalogues.create')
                <a href="{{ route('show_catalogues.create') }}" class="btn">New Entry</a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if(session('success'))<div class="mb-4 text-green-600">{{ session('success') }}</div>@endif
                    <table class="w-full table-auto">
                        <thead>
                            <tr class="text-left"><th class="px-2 py-2">Title</th><th class="px-2 py-2">Version</th><th class="px-2 py-2">Created</th><th class="px-2 py-2">Actions</th></tr>
                        </thead>
                        <tbody>
                            @forelse($catalogues as $c)
                                <tr class="border-t">
                                    <td class="px-2 py-2">
                                        <a href="{{ route('show_catalogues.show', $c) }}" class="text-blue-600">{{ $c->title }}@if($c->choreography_by) By {{ $c->choreography_by }}@endif</a>
                                    </td>
                                    <td class="px-2 py-2">{{ $c->version }}</td>
                                    <td class="px-2 py-2">{{ $c->created_at->toDateString() }}</td>
                                    <td class="px-2 py-2">
                                        @can('show_catalogues.update')<a href="{{ route('show_catalogues.edit', $c) }}" class="text-sm text-blue-600">Edit</a>@endcan
                                        @can('show_catalogues.delete')
                                            <form action="{{ route('show_catalogues.destroy', $c) }}" method="POST" class="inline ml-2">@csrf @method('DELETE')<button class="text-sm text-red-600">Delete</button></form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr><td class="px-2 py-4" colspan="4">No entries.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
