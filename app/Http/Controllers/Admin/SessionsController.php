<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassSession;
use App\Models\DanceGroup;
use App\Services\MonthlyPassService;
use App\Services\ScheduleService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SessionsController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->filled('month')
            ? Carbon::parse($request->get('month'))->startOfMonth()
            : now()->startOfMonth();

        $selectedGroupId = $request->filled('group_id') ? (int) $request->get('group_id') : null;

        $groups = DanceGroup::orderBy('name')->get();

        $query = ClassSession::with('danceGroup')
            ->whereBetween('date', [$month->copy()->startOfMonth()->toDateString(), $month->copy()->endOfMonth()->toDateString()])
            ->orderBy('date')
            ->orderBy('start_time');

        if ($selectedGroupId) {
            $query->where('dance_group_id', $selectedGroupId);
        }

        $sessions = $query->get()->groupBy('date');

        $selectedGroup = $selectedGroupId ? $groups->firstWhere('id', $selectedGroupId) : null;

        return view('admin.sessions.index', compact('month', 'groups', 'selectedGroup', 'selectedGroupId', 'sessions'));
    }

    public function sync(Request $request)
    {
        $validated = $request->validate([
            'month' => ['required', 'date'],
            'group_id' => ['nullable', 'exists:dance_groups,id'],
        ]);

        $count = app(ScheduleService::class)->syncMonth(
            Carbon::parse($validated['month'])->startOfMonth(),
            $validated['group_id'] ?? null
        );

        app(MonthlyPassService::class)->refreshTotalsForMonth(
            Carbon::parse($validated['month'])->startOfMonth(),
            $validated['group_id'] ?? null
        );

        return redirect()->route('admin.sessions.index', [
            'month' => $validated['month'],
            'group_id' => $validated['group_id'] ?? null,
        ])->with('success', __(':count zajęć wygenerowano z planu tygodniowego.', ['count' => $count]));
    }

    public function update(Request $request, ClassSession $session)
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
            'room' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:planned,cancelled'],
        ]);

        $exists = ClassSession::where('dance_group_id', $session->dance_group_id)
            ->whereDate('date', $validated['date'])
            ->where('id', '!=', $session->id)
            ->exists();

        if ($exists) {
            return redirect()->back()->withErrors(['date' => __('Istnieją już zajęcia tej grupy w wybranym dniu.')]);
        }

        $session->update($validated);

        app(MonthlyPassService::class)->refreshTotalsForMonth(
            Carbon::parse($session->date)->startOfMonth(),
            $session->dance_group_id
        );

        return redirect()->route('admin.sessions.index', [
            'month' => Carbon::parse($session->date)->format('Y-m'),
            'group_id' => $session->dance_group_id,
        ])->with('success', __('Zajęcia zaktualizowano.'));
    }
}