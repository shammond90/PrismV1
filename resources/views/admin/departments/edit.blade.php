@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-xl font-semibold mb-4">Edit Department</h1>

    <form action="{{ route('admin.departments.update', $department) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-sm">Name</label>
            <input name="name" value="{{ old('name', $department->name) }}" class="input mt-1 block w-full" required />
        </div>
        <div class="flex gap-2">
            <button class="btn">Save</button>
            <a href="{{ route('admin.departments.index') }}" class="ml-2 text-gray-600">Cancel</a>
        </div>
    </form>
    
    <hr class="my-6">
    <h2 class="text-lg font-semibold mb-3">Positions</h2>
    @if(session('success'))<div class="mb-4 text-green-600">{{ session('success') }}</div>@endif

    <div class="mb-4">
        <form action="{{ route('admin.departments.positions.store', $department) }}" method="POST" class="flex gap-2 items-center">
            @csrf
            <input name="name" placeholder="Position name" class="input">
            <button class="btn">Add Position</button>
        </form>
    </div>

    <table class="min-w-full table-auto">
        <thead><tr><th>Name</th><th></th></tr></thead>
        <tbody>
            @foreach($department->positions as $p)
            <tr>
                <td class="px-2 py-1">{{ $p->name }}</td>
                <td class="px-2 py-1">
                    <a href="{{ route('admin.positions.edit', $p) }}" class="text-blue-600 mr-4">Edit</a>
                    <form method="POST" action="{{ route('admin.positions.destroy', $p) }}" style="display:inline">@csrf @method('DELETE')<button class="text-red-600">Delete</button></form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
