<?php

namespace App\Listeners;

use App\Events\RequestError;
use App\User;
use Illuminate\Support\Facades\Cache;

class CaptureAndSendRequestError
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
     * @param  RequestError  $event
     * @return void
     */
    public function handle(RequestError $event)
    {
        if (isset($event->request_error) && count($event->request_error) > 0) {
            // Fingerprint deliberately excludes 'debug' and 'referer' — both are
            // free text supplied by the reporting client, so including them would
            // let a repeated request bypass the cooldown just by varying that text
            $cache_key = 'error-notification:request:' . md5(
                ($event->request_error['method'] ?? '') . '|' .
                ($event->request_error['source'] ?? '') . '|' .
                ($event->request_error['request_uri'] ?? '') . '|' .
                ($event->request_error['returned_status_code'] ?? '')
            );

            if (Cache::has($cache_key) === false) {
                Cache::put($cache_key, true, now()->addMinutes(self::NOTIFICATION_COOLDOWN_MINUTES));

                $user = User::query()->find(1);
                $user->notify(new \App\Notifications\RequestError($event->request_error));
            }
        }
    }
}
