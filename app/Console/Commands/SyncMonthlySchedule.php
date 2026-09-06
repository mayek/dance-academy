<?php

namespace App\Console\Commands;

use App\Services\ScheduleService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SyncMonthlySchedule extends Command
{
    protected $signature = 'schedule:sync-month {--month= : Y-m month to sync} {--group= : only synchronize one group}';

    protected $description = 'Materialize class_sessions for a given month from each group weekly class_times template';

    public function handle(ScheduleService $schedule): int
    {
        $month = $this->option('month')
            ? Carbon::parse($this->option('month'))
            : now()->startOfMonth();

        $groupId = $this->option('group') ? (int) $this->option('group') : null;

        $count = $schedule->syncMonth($month, $groupId);

        $this->info("Synced {$count} class sessions for " . $month->format('Y-m'));

        return Command::SUCCESS;
    }
}