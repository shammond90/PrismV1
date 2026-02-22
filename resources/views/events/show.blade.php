<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl">Event: {{ $event->name }}</h2>
            <div>
                @can('events.update')
                    <a href="#" class="text-blue-600">Edit</a>
                @endcan
                <a href="{{ route('productions.show', $event->production) }}" class="ml-4 text-gray-600">Back</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded shadow-sm">
                <div><strong>Type:</strong> {{ $event->event_type ?? '—' }}</div>
                <div><strong>When:</strong> {{ $event->start_at->toDateTimeString() }} — {{ $event->end_at?->toDateTimeString() ?? '—' }}</div>
                <div><strong>Space:</strong> {{ $event->space?->name ?? '—' }}</div>
                <div><strong>Departments:</strong> {{ $event->departments ? implode(', ', $event->departments) : '—' }}</div>
                <div class="mt-4"><strong>Notes</strong><div class="mt-1 text-gray-700">{{ $event->notes ?? '—' }}</div></div>

                <div class="mt-6">
                    <form action="{{ route('events.destroy', $event) }}" method="POST" onsubmit="return confirm('Delete this event?')">
                        @csrf
                        @method('DELETE')
                        <button class="text-red-600">Delete Event</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
