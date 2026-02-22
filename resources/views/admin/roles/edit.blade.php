@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Edit Permissions for Role: {{ $role->name }}</h1>

    <form method="POST" action="{{ route('admin.roles.update', $role) }}">
        @csrf

        <div class="mb-4">
            @foreach($permissions as $permission)
                <label class="inline-flex items-center mr-4 mb-2">
                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }} class="mr-2">
                    <span>{{ $permission->name }}</span>
                </label>
            @endforeach
        </div>

        <div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
            <a href="{{ route('admin.roles.index') }}" class="ml-2 text-gray-600">Cancel</a>
        </div>
    </form>
</div>
@endsection
