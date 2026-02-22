@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Roles</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-2 rounded mb-4">{{ session('success') }}</div>
    @endif

    <div class="mb-4">
        <a href="{{ route('admin.roles.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded">Create Role</a>
    </div>

    <table class="w-full table-auto">
        <thead>
            <tr>
                <th class="px-2 py-1 text-left">Name</th>
                <th class="px-2 py-1 text-left">Permissions</th>
                <th class="px-2 py-1">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($roles as $role)
            <tr class="border-t">
                <td class="px-2 py-2">{{ $role->name }}</td>
                <td class="px-2 py-2">{{ $role->permissions->pluck('name')->join(', ') ?: '-' }}</td>
                <td class="px-2 py-2 text-center">
                    <a href="{{ route('admin.roles.edit', $role) }}" class="text-blue-600 mr-4">Edit</a>
                    <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" style="display:inline" onsubmit="return confirm('Delete this role?');">
                        @csrf
                        @method('DELETE')
                        <button class="text-red-600">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
