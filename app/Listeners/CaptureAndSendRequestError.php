<?php

namespace App\Listeners;

use App\Events\RequestError;
use App\User;

class CaptureAndSendRequestError
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
     * @param  RequestError  $event
     * @return void
     */
    public function handle(RequestError $event)
    {
        if (isset($event->request_error) && count($event->request_error) > 0) {
            $user = User::query()->find(1);
            $user->notify(new \App\Notifications\RequestError($event->request_error));
        }
    }
}
