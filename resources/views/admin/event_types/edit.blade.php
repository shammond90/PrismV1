@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-xl font-semibold mb-4">Edit Event Type</h1>

    <form action="{{ route('admin.event_types.update', $eventType) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-sm">Name</label>
            <input name="name" value="{{ old('name', $eventType->name) }}" class="input mt-1 block w-full" required />
        </div>
        <div>
            <label class="block text-sm">Color</label>
            <div class="flex items-center gap-2">
                <input id="color-input" name="color" value="{{ old('color', $eventType->color) }}" class="input mt-1 block w-48" placeholder="#hex, rgb(255,0,0) or 255,0,0">
                <span id="color-preview" class="w-8 h-8 rounded" style="background: {{ $eventType->color ?? '#6b7280' }}"></span>
            </div>
            <p class="text-xs text-gray-400 mt-1">Accepts <code>#aabbcc</code>, <code>rgb(255,0,0)</code> or bare numeric <code>255,0,0</code>.</p>
        </div>
        <div class="flex gap-2">
            <button class="btn">Save</button>
            <a href="{{ route('admin.event_types.index') }}" class="ml-2 text-gray-600">Cancel</a>
        </div>
    </form>

    <script>
        function parseColorInput(val) {
            if (!val) return null;
            val = val.trim();
            var m;
            if (/^#([A-Fa-f0-9]{3})$/.test(val)) {
                m = val.match(/^#([A-Fa-f0-9]{3})$/);
                var s = m[1];
                return '#' + s[0]+s[0] + s[1]+s[1] + s[2]+s[2];
            }
            if (/^#([A-Fa-f0-9]{6})$/.test(val)) return val;
            if (/^rgb\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*\)$/i.test(val)) return val;
            if (/^\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*$/.test(val)) {
                var parts = val.split(',').map(function(s){ return parseInt(s.trim(),10); });
                return 'rgb(' + parts.map(function(n){ return Math.max(0, Math.min(255, n)); }).join(',') + ')';
            }
            if (/^\s*\d{1,3}\s+\d{1,3}\s+\d{1,3}\s*$/.test(val)) {
                var parts = val.trim().split(/\s+/).map(function(s){ return parseInt(s,10); });
                return 'rgb(' + parts.map(function(n){ return Math.max(0, Math.min(255, n)); }).join(',') + ')';
            }
            return val;
        }

        document.getElementById('color-input').addEventListener('input', function(){
            var css = parseColorInput(this.value) || '#6b7280';
            document.getElementById('color-preview').style.background = css;
        });
    </script>
</div>
@endsection
