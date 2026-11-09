<?php

namespace App\Support;

use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

class Notify
{
    /**
     * Send an email without ever breaking the page that triggered it.
     * On shared hosting SMTP can be slow or misconfigured; the admin should still
     * be able to save a shipment. Failures are written to storage/logs/laravel.log.
     */
    public static function send(mixed $to, Mailable $mailable): bool
    {
        try {
            Mail::to($to)->send($mailable);

            return true;
        } catch (\Throwable $e) {
            report($e);

            return false;
        }
    }
}
