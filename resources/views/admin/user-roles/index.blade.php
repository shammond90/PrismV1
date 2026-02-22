@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">User Roles</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-2 rounded mb-4">{{ session('success') }}</div>
    @endif

    <table class="w-full table-auto">
        <thead>
            <tr>
                <th class="px-2 py-1 text-left">Email</th>
                <th class="px-2 py-1 text-left">Name</th>
                <th class="px-2 py-1 text-left">Roles</th>
                <th class="px-2 py-1">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr class="border-t">
                <td class="px-2 py-2">{{ $user->email }}</td>
                <td class="px-2 py-2">{{ $user->name }}</td>
                <td class="px-2 py-2">{{ $user->roles->pluck('name')->join(', ') ?: '-' }}</td>
                <td class="px-2 py-2 text-center">
                    <a href="{{ route('admin.user-roles.edit', $user) }}" class="text-blue-600">Edit</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>
@endsection
