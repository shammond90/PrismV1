@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-6">
    <h2 class="text-lg font-semibold mb-4">New Contact</h2>

    <form action="{{ route('contacts.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 gap-4">
            <input name="title" placeholder="Title" value="{{ old('title') }}" class="input" />
            <input name="first_name" placeholder="First name" value="{{ old('first_name') }}" required class="input" />
            <input name="middle_name" placeholder="Middle name" value="{{ old('middle_name') }}" class="input" />
            <input name="last_name" placeholder="Last name" value="{{ old('last_name') }}" class="input" />
            <input name="given_name" placeholder="Given name" value="{{ old('given_name') }}" class="input" />
            <input name="pronouns" placeholder="Pronouns" value="{{ old('pronouns') }}" class="input" />
            <input name="locations" placeholder="Locations (comma separated)" value="{{ old('locations') }}" class="input" />
            <textarea name="notes" placeholder="Notes" class="input">{{ old('notes') }}</textarea>
        </div>

        <div class="mt-4">
            <button class="btn">Create</button>
            <a href="{{ route('contacts.index') }}" class="ml-4">Cancel</a>
        </div>
    </form>
</div>
@endsection
