<?php

namespace App\Listeners;

use App\Events\InternalError;
use App\User;
use Illuminate\Support\Facades\Cache;

class CaptureAndSendInternalError
{
    private const NOTIFICATION_COOLDOWN_MINUTES = 30;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  InternalError  $event
     * @return void
     */
    public function handle(InternalError $event)
    {
        if (isset($event->internal_error) && count($event->internal_error) > 0) {
            $cache_key = 'error-notification:internal:' . md5(
                ($event->internal_error['message'] ?? '') . '|' .
                ($event->internal_error['file'] ?? '') . '|' .
                ($event->internal_error['line'] ?? '')
            );

            if (Cache::has($cache_key) === false) {
                Cache::put($cache_key, true, now()->addMinutes(self::NOTIFICATION_COOLDOWN_MINUTES));

                $user = User::query()->find(1);
                $user->notify(new \App\Notifications\InternalError($event->internal_error));
            }
        }
    }
}
