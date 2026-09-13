@extends('layouts.app')
@section('title', __('Help / FAQ'))

@section('content')
<div class="px-4 sm:px-0 max-w-4xl">
    <div class="flex items-center gap-3 mb-2">
        <svg class="w-8 h-8 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <h1 class="text-2xl font-bold text-gray-900">{{ __('Help / FAQ') }}</h1>
    </div>
    <p class="text-sm text-gray-600 mb-6">{{ __('This manual explains how the Academy application works. Click a question to reveal the answer with concrete examples.') }}</p>

    {{-- ============ KARNETY ============ --}}
    <h2 class="text-xl font-bold text-purple-700 mt-8 mb-3 flex items-center gap-2">
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">{{ __('Passes') }}</span>
    </h2>

    <div class="space-y-3">
        <details class="bg-white shadow rounded-lg overflow-hidden" open>
            <summary class="cursor-pointer px-5 py-4 text-sm font-semibold text-gray-900 hover:bg-gray-50 flex items-center gap-2">{{ __('What types of passes exist?') }}</summary>
            <div class="px-5 pb-5 text-sm text-gray-600 space-y-2">
                <p>{{ __('There are two kinds of pass types defined in the Passes menu:') }}</p>
                <ul class="list-disc pl-5 space-y-1">
                    <li><strong>{{ __('Single Pass (Karnet jednorazowy)') }}</strong> – {{ __('valid for one day and covers one class.') }}</li>
                    <li><strong>{{ __('Monthly Pass (Karnet miesięczny)') }}</strong> – {{ __('valid for whole calendar months (1, 2, 3, 6 or 12 months). A 1-month pass bought on the 15th is valid from the 1st of that month until the end of that calendar month.') }}</li>
                </ul>
                <p>{{ __('Each pass type has a price. The Name field is optional – if it is left empty, the system generates a name automatically (e.g. “Monthly Pass (3 months)”).') }}</p>
            </div>
        </details>

        <details class="bg-white shadow rounded-lg overflow-hidden">
            <summary class="cursor-pointer px-5 py-4 text-sm font-semibold text-gray-900 hover:bg-gray-50 flex items-center gap-2">{{ __('How are manually added passes created?') }}</summary>
            <div class="px-5 pb-5 text-sm text-gray-600 space-y-2">
                <p>{{ __('A manual pass is a fixed pool of hours that the student can use during the validity period. It is sold to one specific student.') }}</p>
                <p><strong>{{ __('Where you can add it:') }}</strong></p>
                <ul class="list-disc pl-5 space-y-1">
                    <li>{{ __('Dashboard → the green “Buy Pass” button (quick sale, valid from today).') }}</li>
                    <li>{{ __('Payments → “+ Record Payment” (you can choose the start date yourself).') }}</li>
                    <li>{{ __('Student card → “+ Record Payment” modal.') }}</li>
                </ul>
                <p><strong>{{ __('Example:') }}</strong> {{ __('A student trains 2 hours a week for a month. In the pass type “Total hours” you set 8 (2h × 4 weeks) or 10 (2h × 5 weeks). When you sell the pass, every attended lesson subtracts its duration from that pool. When the pool runs out, the pass no longer covers lessons even if the month has not ended.') }}</p>
                <p>{{ __('If you leave “Total hours” empty when selling a monthly pass and the pass type also has no hours, the system calculates the hours automatically from the student’s schedule for the whole validity period and saves them on the pass.') }}</p>
            </div>
        </details>

        <details class="bg-white shadow rounded-lg overflow-hidden">
            <summary class="cursor-pointer px-5 py-4 text-sm font-semibold text-gray-900 hover:bg-gray-50 flex items-center gap-2">{{ __('How do automatically generated monthly passes work?') }}</summary>
            <div class="px-5 pb-5 text-sm text-gray-600 space-y-2">
                <p>{{ __('Each month the system (running on the cron schedule) automatically creates a monthly pass for every student who is enrolled in at least one active group that month.') }}</p>
                <ul class="list-disc pl-5 space-y-1">
                    <li>{{ __('The number of hours is NOT typed by anyone – the system counts it from the student’s real schedule (grafik) for that calendar month: it sums every scheduled lesson in the month (excluding cancelled ones) times its duration.') }}</li>
                    <li>{{ __('Students who join or leave a group in the middle of a month are counted only for the days they were actually enrolled.') }}</li>
                    <li>{{ __('Months with 4 weeks vs 5 weeks are calculated automatically (no manual correction needed).') }}</li>
                    <li>{{ __('The pass is per student, not per group – it covers all the student’s groups.') }}</li>
                    <li>{{ __('The price comes from the monthly lesson fee configured in the system (or the cheapest monthly pass type).') }}</li>
                    <li>{{ __('An automatically generated pass is created as unpaid – you mark it as paid on the dashboard in the “Nieopłacone karnety miesięczne” (Unpaid monthly passes) section with the “Zapłać” button.') }}</li>
                </ul>
                <p><strong>{{ __('Example:') }}</strong> {{ __('A group meets 1 hour every Monday. In a 4-week month the automatic pass has 4 hours, in a 5-week month it has 5 hours. If one Monday is cancelled, only the remaining lessons are counted.') }}</p>
            </div>
        </details>

        <details class="bg-white shadow rounded-lg overflow-hidden">
            <summary class="cursor-pointer px-5 py-4 text-sm font-semibold text-gray-900 hover:bg-gray-50 flex items-center gap-2">{{ __('What is the difference between manual and automatic passes?') }}</summary>
            <div class="px-5 pb-5 text-sm text-gray-600">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase w-1/3">{{ __('Feature') }}</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-purple-800 uppercase bg-purple-50">{{ __('Manually added pass') }}</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-emerald-800 uppercase bg-emerald-50">{{ __('Automatic monthly pass') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-xs text-gray-600">
                            <tr>
                                <td class="px-4 py-2 font-medium text-gray-800">{{ __('Who creates it') }}</td>
                                <td class="px-4 py-2 bg-purple-50/50">{{ __('Staff, by selling a pass (Buy Pass / Record Payment).') }}</td>
                                <td class="px-4 py-2 bg-emerald-50/50">{{ __('The system, once a month, for students enrolled in groups.') }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 font-medium text-gray-800">{{ __('Hours') }}</td>
                                <td class="px-4 py-2 bg-purple-50/50">{{ __('A fixed pool entered by hand; empty = no limit (legacy).') }}</td>
                                <td class="px-4 py-2 bg-emerald-50/50">{{ __('Calculated automatically from the schedule for that month.') }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 font-medium text-gray-800">{{ __('Recalculation') }}</td>
                                <td class="px-4 py-2 bg-purple-50/50">{{ __('None – hours stay as entered.') }}</td>
                                <td class="px-4 py-2 bg-emerald-50/50">{{ __('Yes – after schedule changes (generating or editing sessions) the hours are refreshed.') }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 font-medium text-gray-800">{{ __('Payment status') }}</td>
                                <td class="px-4 py-2 bg-purple-50/50">{{ __('You mark it Paid or Unpaid when selling.') }}</td>
                                <td class="px-4 py-2 bg-emerald-50/50">{{ __('Created as unpaid; mark as paid manually.') }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 font-medium text-gray-800">{{ __('Duration') }}</td>
                                <td class="px-4 py-2 bg-purple-50/50">{{ __('From the chosen start date (single = 1 day, monthly = whole calendar months).') }}</td>
                                <td class="px-4 py-2 bg-emerald-50/50">{{ __('Exactly one calendar month (from the 1st to the last day).') }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 font-medium text-gray-800">{{ __('Where to see it') }}</td>
                                <td class="px-4 py-2 bg-purple-50/50">{{ __('Payments list, student card.') }}</td>
                                <td class="px-4 py-2 bg-emerald-50/50">{{ __('Payments list and the “Unpaid monthly passes” section on the dashboard.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </details>

        <details class="bg-white shadow rounded-lg overflow-hidden">
            <summary class="cursor-pointer px-5 py-4 text-sm font-semibold text-gray-900 hover:bg-gray-50 flex items-center gap-2">{{ __('What happens to the hours when I record attendance?') }}</summary>
            <div class="px-5 pb-5 text-sm text-gray-600 space-y-2">
                <p>{{ __('When you mark a student as Present, the system automatically deducts the lesson duration from the student’s pass:') }}</p>
                <ul class="list-disc pl-5 space-y-1">
                    <li>{{ __('If the student has an active monthly pass for that month, the lesson is billed to that pass (used hours grow).') }}</li>
                    <li>{{ __('If not, the system looks for the oldest active manual pass with remaining hours and subtracts the lesson duration from its pool.') }}</li>
                    <li>{{ __('If there is no pass or no remaining hours, a warning is shown (the lesson is recorded, but no pass covers it).') }}</li>
                    <li>{{ __('A cancelled lesson (status “cancelled”) never consumes hours.') }}</li>
                </ul>
            </div>
        </details>

        <details class="bg-white shadow rounded-lg overflow-hidden">
            <summary class="cursor-pointer px-5 py-4 text-sm font-semibold text-gray-900 hover:bg-gray-50 flex items-center gap-2">{{ __('What happens when a lesson is cancelled or the schedule changes?') }}</summary>
            <div class="px-5 pb-5 text-sm text-gray-600 space-y-2">
                <p>{{ __('After you generate sessions from the weekly plan (“Generuj zajęcia”) or edit a session, the system recalculates the monthly pass hours. The pool of a manual pass is not changed – only automatic monthly passes are refreshed.') }}</p>
                <p>{{ __('Example: a student had 4 lessons planned this month, but one was cancelled. After recalculation the automatic pass shows 3 hours instead of 4.') }}</p>
            </div>
        </details>
    </div>

    {{-- ============ GRAFIK / ZAJĘCIA ============ --}}
    <h2 class="text-xl font-bold text-purple-700 mt-10 mb-3 flex items-center gap-2">
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">{{ __('Schedule & Sessions') }}</span>
    </h2>
    <div class="space-y-3">
        <details class="bg-white shadow rounded-lg overflow-hidden">
            <summary class="cursor-pointer px-5 py-4 text-sm font-semibold text-gray-900 hover:bg-gray-50">{{ __('How do I set up the weekly schedule (grafik)?') }}</summary>
            <div class="px-5 pb-5 text-sm text-gray-600 space-y-2">
                <p>{{ __('Add or edit a group and fill in the “Class Times” table: choose the day of the week, start and end time for each meeting (you can add several in a row). The system remembers which weeks the group runs (Start / End Date).') }}</p>
            </div>
        </details>

        <details class="bg-white shadow rounded-lg overflow-hidden">
            <summary class="cursor-pointer px-5 py-4 text-sm font-semibold text-gray-900 hover:bg-gray-50">{{ __('How do I generate concrete sessions (classes) from the plan?') }}</summary>
            <div class="px-5 pb-5 text-sm text-gray-600 space-y-2">
                <p>{{ __('Go to the Sessions menu, pick a month (and optionally a group) and click “Generuj zajęcia” (Generate sessions). The system turns the weekly plan into individual dated sessions (e.g. “Monday 10:00–11:00” on every Monday of that month). Only the sessions that already exist are kept; the others are created.') }}</p>
                <p>{{ __('You can also correct a single session in this view (change date, time, room or cancel it). After any change the automatic monthly pass hours are recalculated.') }}</p>
            </div>
        </details>
    </div>

    {{-- ============ OBECNOŚCI ============ --}}
    <h2 class="text-xl font-bold text-purple-700 mt-10 mb-3 flex items-center gap-2">
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">{{ __('Attendance') }}</span>
    </h2>
    <div class="space-y-3">
        <details class="bg-white shadow rounded-lg overflow-hidden">
            <summary class="cursor-pointer px-5 py-4 text-sm font-semibold text-gray-900 hover:bg-gray-50">{{ __('How do I record attendance?') }}</summary>
            <div class="px-5 pb-5 text-sm text-gray-600 space-y-2">
                <p>{{ __('In the Attendance menu choose the group and date, then click “Load students”. The list of enrolled students is shown. Mark each one as Present, Absent or Excused and save. Students present on the list who are not enrolled can be added as a one-time entry (“Dodano wejście jednorazowe”).') }}</p>
                <p>{{ __('Each student on the list shows which pass covers their lesson (monthly pass or a fixed-pool manual pass).') }}</p>
            </div>
        </details>

        <details class="bg-white shadow rounded-lg overflow-hidden">
            <summary class="cursor-pointer px-5 py-4 text-sm font-semibold text-gray-900 hover:bg-gray-50">{{ __('How do make-up lessons work?') }}</summary>
            <div class="px-5 pb-5 text-sm text-gray-600 space-y-2">
                <p>{{ __('A student who has an unexcused absence (status Absent) can make up the lesson. This is possible only when all three conditions are true: the make-up takes place within 14 days of the absence, the student had an active pass on the absence day, and the absence has not been made up or excused yet.') }}</p>
                <ul class="list-disc pl-5 space-y-1">
                    <li>{{ __('The make-up can be attended in a different group’s class, not necessarily the student’s own group.') }}</li>
                    <li>{{ __('The original absence changes from Absent to Excused and is marked as made up.') }}</li>
                    <li>{{ __('The make-up lesson is recorded as Present and its hours are deducted from the student’s pass (for automatic monthly passes the hours are added back).') }}</li>
                    <li>{{ __('Cancelled lessons cannot be used for a make-up.') }}</li>
                </ul>
                <p><strong>{{ __('Where:') }}</strong> {{ __('in the Attendance menu under “Make-up” (Odrabianie) you can mark an absence as made up, choosing the group and date of the make-up.') }}</p>
            </div>
        </details>
    </div>

    {{-- ============ UCZNIOWIE / GRUPY ============ --}}
    <h2 class="text-xl font-bold text-purple-700 mt-10 mb-3 flex items-center gap-2">
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-orange-100 text-orange-800">{{ __('Students & Groups') }}</span>
    </h2>
    <div class="space-y-3">
        <details class="bg-white shadow rounded-lg overflow-hidden">
            <summary class="cursor-pointer px-5 py-4 text-sm font-semibold text-gray-900 hover:bg-gray-50">{{ __('How do I add a student and assign them to a group?') }}</summary>
            <div class="px-5 pb-5 text-sm text-gray-600 space-y-2">
                <p>{{ __('In the Students menu click “+ Add Student” and fill in the details. Then open the group you want and use “Assign” to tick the student (a student can belong to several groups simultaneously). When a student leaves, you can remove the assignment – the system then remembers the dates, which matters for automatic monthly pass calculations.') }}</p>
            </div>
        </details>
    </div>

    {{-- ============ PŁATNOŚCI ============ --}}
    <h2 class="text-xl font-bold text-purple-700 mt-10 mb-3 flex items-center gap-2">
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800">{{ __('Payments') }}</span>
    </h2>
    <div class="space-y-3">
        <details class="bg-white shadow rounded-lg overflow-hidden">
            <summary class="cursor-pointer px-5 py-4 text-sm font-semibold text-gray-900 hover:bg-gray-50">{{ __('How do I record a payment or edit an existing pass?') }}</summary>
            <div class="px-5 pb-5 text-sm text-gray-600 space-y-2">
                <p>{{ __('In the Payments menu you can filter by student, group, type and status. The “+ Record Payment” button lets you create any pass manually. You can also open a student’s card and see all their payments, then edit a selected one (change hours, validity, status or mark it as paid/unpaid).') }}</p>
                <p>{{ __('On the dashboard there is also the “Nieopłacone karnety miesięczne” list with a quick “Zapłać” (Pay) button to mark automatic monthly passes as paid.') }}</p>
            </div>
        </details>
    </div>

    {{-- ============ WYDARZENIA ============ --}}
    <h2 class="text-xl font-bold text-purple-700 mt-10 mb-3 flex items-center gap-2">
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">{{ __('Events') }}</span>
    </h2>
    <div class="space-y-3">
        <details class="bg-white shadow rounded-lg overflow-hidden">
            <summary class="cursor-pointer px-5 py-4 text-sm font-semibold text-gray-900 hover:bg-gray-50">{{ __('How do events work?') }}</summary>
            <div class="px-5 pb-5 text-sm text-gray-600 space-y-2">
                <p>{{ __('Events are one-off happenings (e.g. a tournament, a show) with their own date and time. You assign students to an event, and you can sell each student a Single Pass for that event (only single passes are allowed, and a student cannot have two active passes for the same event).') }}</p>
            </div>
        </details>
    </div>

    {{-- ============ RÓLE ============ --}}
    <h2 class="text-xl font-bold text-purple-700 mt-10 mb-3 flex items-center gap-2">
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-200 text-gray-800">{{ __('Accounts & Roles') }}</span>
    </h2>
    <div class="space-y-3">
        <details class="bg-white shadow rounded-lg overflow-hidden">
            <summary class="cursor-pointer px-5 py-4 text-sm font-semibold text-gray-900 hover:bg-gray-50">{{ __('What can each role do?') }}</summary>
            <div class="px-5 pb-5 text-sm text-gray-600 space-y-2">
                <ul class="list-disc pl-5 space-y-1">
                    <li><strong>{{ __('Admin / Reception') }}</strong> – {{ __('full access: dashboard, staff, students, categories, groups, prepayments/passes, attendance, sessions, events.') }}</li>
                    <li><strong>{{ __('Teacher (Instruktor)') }}</strong> – {{ __('sees their own groups, records attendance and can sell a pass to a student from their group.') }}</li>
                    <li><strong>{{ __('Student') }}</strong> – {{ __('sees their own groups, their passes (My Passes) and their attendance history.') }}</li>
                </ul>
            </div>
        </details>

        <details class="bg-white shadow rounded-lg overflow-hidden">
            <summary class="cursor-pointer px-5 py-4 text-sm font-semibold text-gray-900 hover:bg-gray-50">{{ __('Where do I change the language or password?') }}</summary>
            <div class="px-5 pb-5 text-sm text-gray-600 space-y-2">
                <p>{{ __('The language switch (PL / EN) is in the top right corner of every page. To change the password, open your profile (your name in the top right corner) → “Change Password”.') }}</p>
            </div>
        </details>
    </div>
</div>
@endsection