<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::query()->latest('event_date')->latest('published_at')->paginate(15);

        return view('pages.events.index', compact('events'));
    }

    public function create()
    {
        return view('pages.events.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'event_date' => ['nullable', 'date'],
            'location' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:3072'],
            'is_published' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $event = new Event($data);
        $event->is_published = $request->boolean('is_published', true);
        $event->published_at = $event->is_published ? ($data['published_at'] ?? now()) : ($data['published_at'] ?? null);
        $event->sort_order = $data['sort_order'] ?? 0;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('website/events', 'public');
            $event->image = 'storage/' . $path;
        }

        $event->save();

        return redirect()->route('events.index')->with('success', 'Event created successfully.');
    }

    public function edit(Event $event)
    {
        return view('pages.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'event_date' => ['nullable', 'date'],
            'location' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:3072'],
            'is_published' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $event->fill($data);
        $event->is_published = $request->boolean('is_published', false);
        $event->published_at = $event->is_published ? ($data['published_at'] ?? $event->published_at ?? now()) : ($data['published_at'] ?? null);
        $event->sort_order = $data['sort_order'] ?? $event->sort_order ?? 0;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('website/events', 'public');
            $event->image = 'storage/' . $path;
        }

        $event->save();

        return redirect()->route('events.index')->with('success', 'Event updated successfully.');
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()->route('events.index')->with('success', 'Event deleted successfully.');
    }
}
