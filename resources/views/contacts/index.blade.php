<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Contacts') }}</h2>
            @can('contacts.create')
                <a href="{{ route('contacts.create') }}" class="btn">New Contact</a>
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
                                <th class="px-2 py-2">Locations</th>
                                <th class="px-2 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($contacts as $contact)
                                <tr class="border-t">
                                    @php
                                        $cnFirst = $contact->first_name ?? '';
                                        $cnGiven = $contact->given_name ?? '';
                                        $cnLast = $contact->last_name ?? '';
                                        $displayName = trim($cnFirst . ($cnGiven ? " '" . $cnGiven . "'" : '') . ' ' . $cnLast);
                                    @endphp
                                    <td class="px-2 py-2"><a href="{{ route('contacts.show', $contact) }}" class="text-blue-600">{{ $displayName ?: ($contact->full_name ?? '—') }}</a></td>
                                    <td class="px-2 py-2">{{ implode(', ', $contact->locations ?? []) }}</td>
                                    <td class="px-2 py-2">
                                        @can('contacts.update')
                                            <a href="{{ route('contacts.edit', $contact) }}" class="ml-2 text-blue-600">Edit</a>
                                        @endcan
                                        @can('contacts.delete')
                                            <form action="{{ route('contacts.destroy', $contact) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Delete contact?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600">Delete</button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-2 py-4" colspan="3">No contacts found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">{{ $contacts->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
