<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;

class TeacherEventController extends Controller
{
    public function index()
    {
        $events = Event::with(['creator', 'students'])
            ->where('created_by', auth()->id())
            ->latest('date')
            ->paginate(15);
        return view('teacher.events.index', compact('events'));
    }

    public function create()
    {
        return view('teacher.events.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        $validated['created_by'] = auth()->id();

        Event::create($validated);

        return redirect()->route('teacher.events.index')
            ->with('success', 'Event created successfully.');
    }

    public function edit(Event $event)
    {
        abort_unless($event->created_by === auth()->id(), 403);
        return view('teacher.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        abort_unless($event->created_by === auth()->id(), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        $event->update($validated);

        return redirect()->route('teacher.events.index')
            ->with('success', 'Event updated successfully.');
    }

    public function destroy(Event $event)
    {
        abort_unless($event->created_by === auth()->id(), 403);
        $event->delete();
        return redirect()->route('teacher.events.index')
            ->with('success', 'Event deleted successfully.');
    }

    public function assignStudents(Event $event)
    {
        abort_unless($event->created_by === auth()->id(), 403);
        $students = User::where('role', 'student')->get();
        $enrolledIds = $event->students->pluck('id')->toArray();
        return view('teacher.events.assign', compact('event', 'students', 'enrolledIds'));
    }

    public function updateStudents(Request $request, Event $event)
    {
        abort_unless($event->created_by === auth()->id(), 403);

        $validated = $request->validate([
            'student_ids' => ['array'],
            'student_ids.*' => ['exists:users,id'],
        ]);

        $studentIds = $validated['student_ids'] ?? [];
        $event->students()->sync($studentIds);

        return redirect()->route('teacher.events.index')
            ->with('success', 'Students assigned successfully.');
    }
}
