<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\Payment;
use App\Services\MonthlyPassService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RecomputePassHistory extends Command
{
    protected $signature = 'passes:recompute-history';

    protected $description = 'Convert legacy per-group monthly passes into the new per-student monthly model';

    public function handle(MonthlyPassService $service): int
    {
        $legacyPasses = Payment::where('pass_type', 'monthly')
            ->whereNotNull('dance_group_id')
            ->where('status', 'active')
            ->get();

        $grouped = $legacyPasses->groupBy(fn ($p) => $p->student_id . '|' . $p->valid_from->format('Y-m'));

        $converted = 0;

        foreach ($grouped as $key => $passes) {
            [$studentId, $monthKey] = explode('|', $key);
            $month = Carbon::parse($monthKey . '-01')->startOfMonth();

            $existing = Payment::where('student_id', $studentId)
                ->where('pass_type', 'monthly')
                ->whereNull('dance_group_id')
                ->whereDate('valid_from', $month->toDateString())
                ->exists();

            if ($existing) {
                continue;
            }

            $allPaid = $passes->every(fn ($p) => $p->is_paid);
            $amount = $passes->sum('amount');
            $totalHours = $passes->sum('total_hours');
            $usedHours = $passes->sum('used_hours');

            $combined = Payment::create([
                'student_id' => $studentId,
                'dance_group_id' => null,
                'pass_type' => 'monthly',
                'pass_type_id' => $passes->first()->pass_type_id,
                'amount' => $amount,
                'total_hours' => $totalHours,
                'used_hours' => $usedHours,
                'valid_from' => $month->copy()->startOfDay(),
                'valid_until' => $month->copy()->endOfMonth()->endOfDay(),
                'status' => 'active',
                'is_paid' => $allPaid,
                'source' => 'legacy',
                'recorded_by' => null,
                'notes' => __('Połączone z :count karnetów grupowych (historyczne)', ['count' => $passes->count()]),
            ]);

            DB::transaction(function () use ($passes, $month, $studentId, $combined) {
                Attendance::where('student_id', $studentId)
                    ->whereBetween('date', [$month->copy()->startOfDay(), $month->copy()->endOfMonth()->endOfDay()])
                    ->whereIn('payment_id', $passes->pluck('id'))
                    ->update(['payment_id' => $combined->id]);

                $passes->each->update([
                    'status' => 'cancelled',
                    'notes' => trim(($passes->first()->notes ?? '') . ' ' . __('Przeniesione do karnetu #:id', ['id' => $combined->id])),
                ]);
            });

            $service->recomputeUsedHours($combined);
            $converted++;
        }

        $this->info("Converted {$converted} month(s) of legacy per-group passes into per-student monthly passes");

        return Command::SUCCESS;
    }
}