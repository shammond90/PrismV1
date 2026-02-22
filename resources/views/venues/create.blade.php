@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-6">
    <h2 class="text-lg font-semibold mb-4">New Venue</h2>

    @if($errors->any())
        <div class="mb-4 text-red-600">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white shadow sm:rounded-lg p-6">
        <form action="{{ route('venues.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 gap-4">
                <input name="name" value="{{ old('name') }}" placeholder="Venue name" class="input" required />
                <input name="type" value="{{ old('type') }}" placeholder="Type (e.g., complex)" class="input" />
                <input name="website" value="{{ old('website') }}" placeholder="Website" class="input" />
                <textarea name="notes" placeholder="Notes" class="input">{{ old('notes') }}</textarea>
                <div>
                    <button class="btn">Create</button>
                    <a href="{{ route('venues.index') }}" class="ml-2 text-gray-600">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
