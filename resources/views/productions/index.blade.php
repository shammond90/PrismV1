<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Productions') }}</h2>
            @can('productions.create')
                <a href="{{ route('productions.create') }}" class="btn">New Production</a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <table class="w-full table-auto">
                        <thead><tr class="text-left"><th>Title</th><th>Show</th><th>Start</th><th>End</th><th>Actions</th></tr></thead>
                        <tbody>
                            @forelse($productions as $p)
                                <tr class="border-t">
                                    <td class="px-2 py-2"><a href="{{ route('productions.show', $p) }}" class="text-blue-600">{{ $p->title }}</a></td>
                                    <td class="px-2 py-2">{{ $p->show->title }}</td>
                                    <td class="px-2 py-2">{{ optional($p->start_date)->toDateString() }}</td>
                                    <td class="px-2 py-2">{{ optional($p->end_date)->toDateString() }}</td>
                                    <td class="px-2 py-2">
                                        @can('productions.update')<a href="{{ route('productions.edit', $p) }}" class="text-blue-600">Edit</a>@endcan
                                        @can('productions.delete')<form action="{{ route('productions.destroy', $p) }}" method="POST" class="inline ml-2">@csrf @method('DELETE')<button class="text-red-600">Delete</button></form>@endcan
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-2 py-4">No productions.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
