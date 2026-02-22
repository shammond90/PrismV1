@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-xl font-semibold mb-4">Departments</h1>

    @if(session('success'))<div class="mb-4 text-green-600">{{ session('success') }}</div>@endif

    <div class="mb-6">
        <form action="{{ route('admin.departments.store') }}" method="POST" class="flex gap-2">
            @csrf
            <input name="name" placeholder="Name" class="input">
            <button class="btn">Add</button>
        </form>
    </div>

    <table class="min-w-full table-auto">
        <thead><tr><th>Name</th><th></th></tr></thead>
        <tbody>
            @foreach($items as $i)
            <tr>
                <td class="px-2 py-1">{{ $i->name }}</td>
                <td class="px-2 py-1">
                    <a href="{{ route('admin.departments.edit', $i) }}" class="text-blue-600 mr-4">Edit</a>
                    <form method="POST" action="{{ route('admin.departments.destroy', $i) }}" style="display:inline">@csrf @method('DELETE')<button class="text-red-600">Delete</button></form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
