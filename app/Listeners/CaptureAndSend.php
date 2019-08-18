<?php

namespace App\Listeners;

use App\Events\RequestError;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CaptureAndSend
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
            // Send an email

        }
    }
}
