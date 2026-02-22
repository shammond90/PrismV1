@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-xl font-semibold mb-4">Edit Position</h1>

    <form action="{{ route('admin.positions.update', $position) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-sm">Name</label>
            <input name="name" value="{{ old('name', $position->name) }}" class="input mt-1 block w-full" required />
        </div>
        <div class="flex gap-2">
            <button class="btn">Save</button>
            <a href="{{ route('admin.departments.edit', $position->department) }}" class="ml-2 text-gray-600">Cancel</a>
        </div>
    </form>
</div>
@endsection
