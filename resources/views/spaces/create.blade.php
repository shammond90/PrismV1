@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-6">
    <h2 class="text-lg font-semibold mb-4">New Space</h2>

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
        <form action="{{ route('spaces.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 gap-4">
                <select name="building_id" class="input">
                    <option value="">No building</option>
                    @foreach($buildings as $b)
                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                    @endforeach
                </select>
                <input name="name" value="{{ old('name') }}" placeholder="Space name" class="input" required />
                <input name="type" value="{{ old('type') }}" placeholder="Type (conference, office, virtual)" class="input" />
                <textarea name="notes" placeholder="Notes" class="input">{{ old('notes') }}</textarea>
                <div>
                    <button class="btn">Create</button>
                    <a href="{{ route('spaces.index') }}" class="ml-2 text-gray-600">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
