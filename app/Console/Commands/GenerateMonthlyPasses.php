<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\MonthlyPassService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateMonthlyPasses extends Command
{
    protected $signature = 'passes:generate-month {--month= : Y-m month to generate obligations for}';

    protected $description = 'Generate monthly per-student pass obligations for all enrolled students';

    public function handle(MonthlyPassService $service): int
    {
        $month = $this->option('month')
            ? Carbon::parse($this->option('month'))->startOfMonth()
            : now()->startOfMonth();

        $students = User::where('role', 'student')
            ->whereHas('enrolledGroups', function ($q) use ($month) {
                $q->where(fn ($qq) => $qq->whereNull('end_date')->orWhere('end_date', '>=', $month->toDateString()));
            })
            ->get();

        $skipped = $created = 0;

        foreach ($students as $student) {
            $pass = $service->ensureObligation($student, $month);

            if ($pass === null) {
                $skipped++;
                continue;
            }

            $created++;
        }

        $this->info("Month: {$month->format('Y-m')}");
        $this->info("Obligations ensured for {$created} student(s), skipped {$skipped} without enrollment");

        return Command::SUCCESS;
    }
}