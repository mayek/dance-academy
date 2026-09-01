<?php

use App\Console\Commands\SendExpiringPassReminders;
use Illuminate\Support\Facades\Schedule;

Schedule::command('passes:send-expiring-reminders')->dailyAt('18:00');
