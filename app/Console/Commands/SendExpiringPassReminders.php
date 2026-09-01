<?php

namespace App\Console\Commands;

use App\Mail\ExpiringPassReminder;
use App\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendExpiringPassReminders extends Command
{
    protected $signature = 'passes:send-expiring-reminders';

    protected $description = 'Send email reminders for monthly passes expiring in 7 days';

    public function handle()
    {
        $targetDate = now()->addDays(7);

        $expiringPasses = Payment::with(['student', 'danceGroup'])
            ->where('pass_type', 'monthly')
            ->where('status', 'active')
            ->whereDate('valid_until', $targetDate)
            ->get();

        if ($expiringPasses->isEmpty()) {
            $this->info('No expiring passes found for ' . $targetDate->format('Y-m-d'));
            return Command::SUCCESS;
        }

        $recipient = app()->environment('production')
            ? null
            : 'darek.maju@gmail.com';

        foreach ($expiringPasses as $payment) {
            $email = $recipient ?? $payment->student->email;

            Mail::to($email)->send(new ExpiringPassReminder($payment));

            $this->info('Reminder sent to ' . $email . ' for pass #' . $payment->id);
        }

        $this->info('Sent ' . $expiringPasses->count() . ' reminder(s)');

        return Command::SUCCESS;
    }
}
