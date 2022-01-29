<?php

namespace App\Listeners;

use App\Events\InternalError;
use App\User;

class CaptureAndSendInternalError
{
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
            $user = User::query()->find(1);
            $user->notify(new \App\Notifications\InternalError($event->internal_error));
        }
    }
}
