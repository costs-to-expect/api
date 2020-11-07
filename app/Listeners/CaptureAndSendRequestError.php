<?php

namespace App\Listeners;

use App\Events\RequestError;
use App\Mail\RequestError as RequestErrorMail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

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
            Mail::to(Config::get('api.mail.request-error.to'))->
                send(
                    new RequestErrorMail($event->request_error)
                );
        }
    }
}
