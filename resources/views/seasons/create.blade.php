<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Create Season') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('seasons.store') }}" method="POST">
                        @csrf
                        <div class="grid gap-4">
                            <label class="block">
                                <span class="text-sm text-gray-700">Name</span>
                                <input name="name" value="{{ old('name') }}" class="input mt-1 block w-full" />
                            </label>

                            <div class="flex gap-2">
                                <label class="block flex-1">
                                    <span class="text-sm text-gray-700">Start Date</span>
                                    <input type="date" name="start_date" value="{{ old('start_date') }}" class="input mt-1 block w-full" />
                                </label>
                                <label class="block flex-1">
                                    <span class="text-sm text-gray-700">End Date</span>
                                    <input type="date" name="end_date" value="{{ old('end_date') }}" class="input mt-1 block w-full" />
                                </label>
                            </div>

                            <label class="block">
                                <span class="text-sm text-gray-700">Notes</span>
                                <textarea name="notes" class="input mt-1 block w-full">{{ old('notes') }}</textarea>
                            </label>

                            <div class="flex gap-2">
                                <button class="btn">Create</button>
                                <a href="{{ route('seasons.index') }}" class="ml-2 text-gray-600">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
