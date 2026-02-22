@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-6">
    <h2 class="text-lg font-semibold mb-4">Edit Contact</h2>

    <form action="{{ route('contacts.update', $contact) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-4">
            <input name="title" placeholder="Title" value="{{ old('title', $contact->title) }}" class="input" />
            <input name="first_name" placeholder="First name" value="{{ old('first_name', $contact->first_name) }}" required class="input" />
            <input name="middle_name" placeholder="Middle name" value="{{ old('middle_name', $contact->middle_name) }}" class="input" />
            <input name="last_name" placeholder="Last name" value="{{ old('last_name', $contact->last_name) }}" class="input" />
            <input name="given_name" placeholder="Given name" value="{{ old('given_name', $contact->given_name) }}" class="input" />
            <input name="pronouns" placeholder="Pronouns" value="{{ old('pronouns', $contact->pronouns) }}" class="input" />
            <input name="locations" placeholder="Locations (comma separated)" value="{{ old('locations', implode(', ', $contact->locations ?? [])) }}" class="input" />
            <textarea name="notes" placeholder="Notes" class="input">{{ old('notes', $contact->notes) }}</textarea>
        </div>

        <div class="mt-4">
            <button class="btn">Update</button>
            <a href="{{ route('contacts.index') }}" class="ml-4">Cancel</a>
        </div>
    </form>
</div>
@endsection
