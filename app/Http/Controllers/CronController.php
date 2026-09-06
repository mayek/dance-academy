<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class CronController extends Controller
{
    public function expiringPassReminders(Request $request)
    {
        $token = config('cron.token');

        if (! $token || ! hash_equals((string) $token, (string) $request->query('token', ''))) {
            abort(404);
        }

        Artisan::call('passes:send-expiring-reminders');

        return response(Artisan::output())->header('Content-Type', 'text/plain');
    }

    public function generateMonthlyPasses(Request $request)
    {
        $token = config('cron.token');

        if (! $token || ! hash_equals((string) $token, (string) $request->query('token', ''))) {
            abort(404);
        }

        Artisan::call('schedule:sync-month');
        Artisan::call('passes:generate-month');

        return response(Artisan::output())->header('Content-Type', 'text/plain');
    }
}
