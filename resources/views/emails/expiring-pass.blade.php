<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Your monthly pass is expiring soon') }}</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; background-color: #f9fafb; padding: 24px;">
    <div style="max-width: 480px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; padding: 32px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <h1 style="font-size: 20px; font-weight: bold; color: #1f2937; margin: 0 0 16px;">{{ __('Dance Academy') }}</h1>
        <p style="font-size: 14px; color: #6b7280; margin: 0 0 16px;">{{ __('Hello') }} <strong>{{ $payment->student->first_name }}</strong>,</p>
        <p style="font-size: 14px; color: #374151; margin: 0 0 16px;">
            {{ __('Your monthly pass for the group') }} <strong>{{ $payment->danceGroup->name }}</strong>
            {{ __('will expire on') }} <strong>{{ $payment->valid_until->format('d.m.Y') }}</strong>.
        </p>
        <p style="font-size: 14px; color: #374151; margin: 0 0 24px;">
            {{ __('Please renew your pass to continue attending classes without interruption.') }}
        </p>
        <a href="{{ route('student.payments.create', ['group_id' => $payment->dance_group_id]) }}" style="display: inline-block; background-color: #059669; color: #ffffff; font-size: 14px; font-weight: 600; padding: 10px 20px; border-radius: 6px; text-decoration: none;">
            {{ __('Renew Pass') }}
        </a>
        <p style="font-size: 12px; color: #9ca3af; margin: 24px 0 0;">{{ __('Thank you for being with us!') }}</p>
    </div>
</body>
</html>
