<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EventType;

class EventTypeController extends Controller
{
    public function index()
    {
        $types = EventType::orderBy('name')->get();
        return view('admin.event_types.index', compact('types'));
    }

    public function edit(EventType $eventType)
    {
        return view('admin.event_types.edit', compact('eventType'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:event_types,name',
            'color' => 'nullable|string|max:50'
        ]);
        if (!empty($data['color'])) {
            $norm = $this->normalizeColor($data['color']);
            if (!$norm) {
                return back()->withErrors(['color' => 'Color must be a hex code like #aabbcc or rgb(255,0,0)'])->withInput();
            }
            $data['color'] = $norm;
        }

        EventType::create($data);
        return redirect()->route('admin.event_types.index')->with('success','Event type added.');
    }

    public function destroy(EventType $eventType)
    {
        $eventType->delete();
        return redirect()->route('admin.event_types.index')->with('success','Event type removed.');
    }

    public function update(Request $request, EventType $eventType)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:event_types,name,' . $eventType->id,
            'color' => 'nullable|string|max:50'
        ]);
        if (!empty($data['color'])) {
            $norm = $this->normalizeColor($data['color']);
            if (!$norm) {
                return back()->withErrors(['color' => 'Color must be a hex code like #aabbcc or rgb(255,0,0)'])->withInput();
            }
            $data['color'] = $norm;
        }

        $eventType->update($data);
        return redirect()->route('admin.event_types.index')->with('success','Event type updated.');
    }

    /**
     * Normalize a color string to 6-char hex (e.g. #rrggbb) if possible.
     * Accepts: #rgb, #rrggbb, rgb(r,g,b) with optional spaces.
     * Returns normalized hex string or null if invalid.
     */
    private function normalizeColor(?string $color)
    {
        if (!$color) return null;
        $c = trim($color);

        // Hex (#abc or #aabbcc)
        if (preg_match('/^#([A-Fa-f0-9]{3})$/', $c, $m)) {
            $short = $m[1];
            $r = str_repeat($short[0],2);
            $g = str_repeat($short[1],2);
            $b = str_repeat($short[2],2);
            return '#' . strtolower($r.$g.$b);
        }
        if (preg_match('/^#([A-Fa-f0-9]{6})$/', $c, $m)) {
            return '#' . strtolower($m[1]);
        }

        // rgb(r,g,b)
        if (preg_match('/^rgb\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*\)$/i', $c, $m)) {
            $r = max(0, min(255, intval($m[1])));
            $g = max(0, min(255, intval($m[2])));
            $b = max(0, min(255, intval($m[3])));
            return sprintf('#%02x%02x%02x', $r, $g, $b);
        }

        // Bare numeric RGB: "255,0,0" or "255 0 0" (no rgb() wrapper)
        if (preg_match('/^\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*$/', $c, $m)) {
            $r = max(0, min(255, intval($m[1])));
            $g = max(0, min(255, intval($m[2])));
            $b = max(0, min(255, intval($m[3])));
            return sprintf('#%02x%02x%02x', $r, $g, $b);
        }
        if (preg_match('/^\s*(\d{1,3})\s+(\d{1,3})\s+(\d{1,3})\s*$/', $c, $m)) {
            $r = max(0, min(255, intval($m[1])));
            $g = max(0, min(255, intval($m[2])));
            $b = max(0, min(255, intval($m[3])));
            return sprintf('#%02x%02x%02x', $r, $g, $b);
        }

        return null;
    }
}
