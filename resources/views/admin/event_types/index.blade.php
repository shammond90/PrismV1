@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-xl font-semibold mb-4">Event Types</h1>

    @if(session('success'))<div class="mb-4 text-green-600">{{ session('success') }}</div>@endif

    <div class="mb-6">
        <form action="{{ route('admin.event_types.store') }}" method="POST" class="flex gap-2 items-center">
            @csrf
            <input name="name" placeholder="Name" class="input">
            <input id="new-color-input" name="color" placeholder="#hex, rgb(255,0,0) or 255,0,0" class="input" value="#6b7280">
            <span id="new-color-preview" class="w-8 h-8 rounded inline-block" style="background:#6b7280"></span>
            <button class="btn">Add</button>
        </form>
    </div>

    <script>
        function parseColorInput(val) {
            if (!val) return null;
            val = val.trim();
            // hex #abc or #aabbcc
            var m;
            if (/^#([A-Fa-f0-9]{3})$/.test(val)) {
                m = val.match(/^#([A-Fa-f0-9]{3})$/);
                var s = m[1];
                return '#' + s[0]+s[0] + s[1]+s[1] + s[2]+s[2];
            }
            if (/^#([A-Fa-f0-9]{6})$/.test(val)) return val;
            // rgb(...) form
            if (/^rgb\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*\)$/i.test(val)) return val;
            // bare numeric: "255,0,0" or "255 0 0"
            if (/^\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*$/.test(val)) {
                var parts = val.split(',').map(function(s){ return parseInt(s.trim(),10); });
                return 'rgb(' + parts.map(function(n){ return Math.max(0, Math.min(255, n)); }).join(',') + ')';
            }
            if (/^\s*\d{1,3}\s+\d{1,3}\s+\d{1,3}\s*$/.test(val)) {
                var parts = val.trim().split(/\s+/).map(function(s){ return parseInt(s,10); });
                return 'rgb(' + parts.map(function(n){ return Math.max(0, Math.min(255, n)); }).join(',') + ')';
            }
            // fallback to original value (named colors ok)
            return val;
        }

        document.getElementById('new-color-input').addEventListener('input', function(){
            var css = parseColorInput(this.value) || '#6b7280';
            document.getElementById('new-color-preview').style.background = css;
        });
    </script>

    <table class="min-w-full table-auto">
        <thead><tr><th>Name</th><th>Color</th><th></th></tr></thead>
        <tbody>
            @foreach($types as $t)
            <tr>
                <td class="px-2 py-1">{{ $t->name }}</td>
                <td class="px-2 py-1"><span class="inline-block w-6 h-6" style="background:{{ $t->color }}"></span> {{ $t->color }}</td>
                <td class="px-2 py-1">
                    <a href="{{ route('admin.event_types.edit', $t) }}" class="text-blue-600 mr-4">Edit</a>
                    <form method="POST" action="{{ route('admin.event_types.destroy', $t) }}" style="display:inline">@csrf @method('DELETE')<button class="text-red-600">Delete</button></form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
