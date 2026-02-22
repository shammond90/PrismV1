<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Production;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Production $production, Request $request)
    {
        // JSON for calendar
        if ($request->wantsJson() || $request->query('json')) {
            $events = $production->events()->with('space')->get()->map(function($e){
                return [
                    'id' => $e->id,
                    'title' => $e->name,
                    'start' => $e->start_at->toDateTimeString(),
                    'end' => $e->end_at ? $e->end_at->toDateTimeString() : null,
                    'color' => self::colorForType($e->event_type),
                    'extendedProps' => [
                        'event_type' => $e->event_type,
                        'space' => $e->space?->name,
                        'departments' => $e->departments,
                        'notes' => $e->notes,
                        'production_id' => $e->production_id,
                    ],
                ];
            });
            return response()->json($events);
        }

        $events = $production->events()->with('space')->orderBy('start_at')->get();
        return view('productions.partials.scheduling', compact('production','events'));
    }

    public static function colorForType($type)
    {
        // Prefer configured EventType in DB
        try {
            $et = \App\Models\EventType::where('name', $type)->first();
            if ($et && $et->color) return $et->color;
        } catch (\Exception $e) {
            // ignore DB errors
        }

        $map = [
            'Rehearsal'   => '#1f8ef1',
            'Performance' => '#10b981',
            'Tech'        => '#f59e0b',
            'Load-in'     => '#ef4444',
            'Load-out'    => '#ef4444',
            'Meeting'     => '#8b5cf6',
            'Other'       => '#6b7280',
        ];
        return $map[$type] ?? '#6b7280';
    }

    public function store(Production $production, Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'event_type' => 'nullable|string|max:100',
            'start_at' => 'required|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'space_id' => 'nullable|exists:spaces,id',
            'departments' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        $data['production_id'] = $production->id;
        $event = Event::create($data);

        if ($request->wantsJson()) return response()->json($event, 201);
        return redirect()->to(route('productions.show', $production) . '#scheduling')->with('success','Event created.');
    }

    public function show(Event $event)
    {
        if (request()->wantsJson() || request()->query('json')) {
            return response()->json($event->load('space'));
        }
        return view('events.show', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        // PATCH from drag/drop only sends start_at & end_at
        if ($request->isMethod('PATCH') && !$request->has('name')) {
            $data = $request->validate([
                'start_at' => 'required|date',
                'end_at'   => 'nullable|date',
            ]);
            $event->update($data);
            return response()->json($event);
        }

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'event_type'  => 'nullable|string|max:100',
            'start_at'    => 'required|date',
            'end_at'      => 'nullable|date|after_or_equal:start_at',
            'space_id'    => 'nullable|exists:spaces,id',
            'departments' => 'nullable|array',
            'notes'       => 'nullable|string',
        ]);

        $event->update($data);
        if ($request->wantsJson()) return response()->json($event);
        return redirect()->to(route('productions.show', $event->production) . '#scheduling')->with('success','Event updated.');
    }

    public function destroy(Event $event)
    {
        $prod = $event->production;
        $event->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->to(route('productions.show', $prod) . '#scheduling')->with('success','Event deleted.');
    }
}
