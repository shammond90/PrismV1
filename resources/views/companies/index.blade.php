<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Companies') }}</h2>
            @can('companies.create')
                <a href="{{ route('companies.create') }}" class="btn">New Company</a>
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
                                <th class="px-2 py-2">Industry</th>
                                <th class="px-2 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($companies as $company)
                                <tr class="border-t">
                                    <td class="px-2 py-2"><a href="{{ route('companies.show', $company) }}" class="text-blue-600">{{ $company->name }}</a></td>
                                    <td class="px-2 py-2">{{ $company->industry }}</td>
                                    <td class="px-2 py-2">
                                        @can('companies.update')
                                            <a href="{{ route('companies.edit', $company) }}" class="ml-2 text-blue-600">Edit</a>
                                        @endcan
                                        @can('companies.delete')
                                            <form action="{{ route('companies.destroy', $company) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Delete company?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600">Delete</button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-2 py-4" colspan="3">No companies found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">{{ $companies->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
