<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Services\MonthlyPassService;
use Illuminate\Console\Command;

class RecomputePassHours extends Command
{
    protected $signature = 'passes:recompute-hours';

    protected $description = 'Fill in automatically calculated hours for monthly passes that have no total_hours';

    public function handle(MonthlyPassService $service): int
    {
        $payments = Payment::query()
            ->where('pass_type', 'monthly')
            ->whereNull('total_hours')
            ->where('status', '!=', 'cancelled')
            ->with('student')
            ->get();

        $filled = 0;

        foreach ($payments as $payment) {
            if ($payment->student === null) {
                continue;
            }

            $computed = $service->computeHoursBetween(
                $payment->student,
                $payment->valid_from,
                $payment->valid_until
            );

            if ($computed <= 0) {
                continue;
            }

            $payment->total_hours = $computed;
            $payment->save();
            $filled++;
        }

        $this->info("Filled hours for {$filled} monthly pass(es) of {$payments->count()} without total_hours");

        return Command::SUCCESS;
    }
}