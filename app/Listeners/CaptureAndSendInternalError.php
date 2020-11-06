<?php

namespace App\Listeners;

use App\Events\InternalError;
use App\Mail\InternalError as InternalErrorMail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

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
            Mail::to(Config::get('api.mail.internal-error.to'))->
                send(
                    new InternalErrorMail($event->internal_error)
                );
        }
    }
}
