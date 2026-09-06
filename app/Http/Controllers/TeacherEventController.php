<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\PassType;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;

class TeacherEventController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search'));

        $events = Event::with(['creator', 'teacher', 'students'])
            ->where('teacher_id', auth()->id())
            ->when($search !== '', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhereHas('students', function ($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%");
                    });
            })
            ->latest('date')
            ->paginate(15)
            ->withQueryString();

        $students = User::where('role', 'student')->with('enrolledGroups')->orderBy('first_name')->get();
        $passTypes = PassType::where('type', 'single')->orderBy('price')->get();

        return view('teacher.events.index', compact('events', 'students', 'passTypes'));
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
            'room' => ['nullable', 'string', 'in:górna,dolna'],
        ]);

        $validated['created_by'] = auth()->id();
        $validated['teacher_id'] = auth()->id();

        Event::create($validated);

        return redirect()->route('teacher.events.index')
            ->with('success', __('Event created successfully.'));
    }

    public function edit(Event $event)
    {
        abort_unless($event->teacher_id === auth()->id(), 403);
        return view('teacher.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        abort_unless($event->teacher_id === auth()->id(), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'room' => ['nullable', 'string', 'in:górna,dolna'],
        ]);

        $event->update($validated);

        return redirect()->route('teacher.events.index')
            ->with('success', __('Event updated successfully.'));
    }

    public function destroy(Event $event)
    {
        abort_unless($event->teacher_id === auth()->id(), 403);
        $event->delete();
        return redirect()->route('teacher.events.index')
            ->with('success', __('Event deleted successfully.'));
    }

public function assignStudents(Event $event)
    {
        abort_unless($event->teacher_id === auth()->id(), 403);
        $students = User::where('role', 'student')->orderBy('first_name')->get();
        $enrolledIds = $event->students->pluck('id')->toArray();
        $students = $students->sortByDesc(fn (User $student) => in_array($student->id, $enrolledIds))->values();
        return view('teacher.events.assign', compact('event', 'students', 'enrolledIds'));
    }

    public function updateStudents(Request $request, Event $event)
    {
        abort_unless($event->teacher_id === auth()->id(), 403);

        $validated = $request->validate([
            'student_ids' => ['array'],
            'student_ids.*' => ['exists:users,id'],
        ]);

        $studentIds = $validated['student_ids'] ?? [];
        $event->students()->sync($studentIds);

        return redirect()->route('teacher.events.index')
            ->with('success', __('Students assigned successfully.'));
    }

    public function buyPass(Request $request)
    {
        $validated = $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'student_id' => ['required', 'exists:users,id'],
            'pass_type_id' => ['required', 'exists:pass_types,id'],
            'is_paid' => ['nullable', 'boolean'],
        ]);

        $event = Event::findOrFail($validated['event_id']);
        abort_unless($event->teacher_id === auth()->id(), 403);

        $passType = PassType::findOrFail($validated['pass_type_id']);
        abort_unless($passType->isSingle(), 422);

        $alreadyActive = Payment::where('student_id', $validated['student_id'])
            ->where('event_id', $event->id)
            ->where('status', 'active')
            ->exists();

        if ($alreadyActive) {
            return back()->withErrors(['student_id' => __('This student already has an active pass for this event.')]);
        }

        $validated['dance_group_id'] = null;
        $validated['event_id'] = $event->id;
        $validated['pass_type'] = $passType->type;
        $validated['pass_type_id'] = $passType->id;
        $validated['amount'] = $passType->price;
        $validated['valid_from'] = $event->date->copy()->startOfDay();
        $validated['valid_until'] = $event->date->copy()->endOfDay();
        $validated['status'] = $event->date->lt(now()->startOfDay()) ? 'expired' : 'active';
        $validated['recorded_by'] = auth()->id();
        $validated['notes'] = null;
        $validated['is_paid'] = $request->boolean('is_paid', true);

        Payment::create($validated);
        $event->students()->syncWithoutDetaching([$validated['student_id']]);

        return redirect()->route('teacher.events.index')
            ->with('success', __('Pass purchased successfully.'));
    }
}
