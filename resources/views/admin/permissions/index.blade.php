@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Permissions</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-2 rounded mb-4">{{ session('success') }}</div>
    @endif

    <div class="mb-4">
        <a href="{{ route('admin.permissions.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded">Create Permission</a>
    </div>

    <table class="w-full table-auto">
        <thead>
            <tr>
                <th class="px-2 py-1 text-left">Name</th>
                <th class="px-2 py-1">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($permissions as $permission)
            <tr class="border-t">
                <td class="px-2 py-2">{{ $permission->name }}</td>
                <td class="px-2 py-2 text-center">
                    <form method="POST" action="{{ route('admin.permissions.destroy', $permission) }}" onsubmit="return confirm('Delete this permission?');">
                        @csrf
                        @method('DELETE')
                        <button class="text-red-600">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $permissions->links() }}
    </div>
</div>
@endsection
