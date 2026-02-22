@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Create Permission</h1>

    <form method="POST" action="{{ route('admin.permissions.store') }}">
        @csrf

        <div class="mb-4">
            <label class="block mb-1">Name</label>
            <input name="name" class="border rounded px-2 py-1 w-full" value="{{ old('name') }}">
            @error('name') <div class="text-red-600 mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Create</button>
            <a href="{{ route('admin.permissions.index') }}" class="ml-2 text-gray-600">Cancel</a>
        </div>
    </form>
</div>
@endsection
