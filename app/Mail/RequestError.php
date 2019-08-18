<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Config;

class RequestError extends Mailable
{
    use Queueable, SerializesModels;

    public $api_from_mail;
    public $request_error;

    /**
     * Create a new message instance.
     *
     * @param array $request_error
     *
     * @return void
     */
    public function __construct(array $request_error)
    {
        $this->api_from_mail = Config::get('api.mail.request-error.from');
        $this->request_error = $request_error;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->api_from_mail)->
            view('mail.request-error')->
            subject('Costs to Expect API: Request error');
    }
}
