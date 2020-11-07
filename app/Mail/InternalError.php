<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;

class InternalError extends Mailable
{
    use Queueable, SerializesModels;

    public $api_from_mail;
    public $api_from_name;
    public $internal_error;

    /**
     * Create a new message instance.
     *
     * @param array $internal_error
     *
     * @return void
     */
    public function __construct(array $internal_error)
    {
        $this->api_from_mail = Config::get('api.mail.internal-error.from_mail');
        $this->api_from_name = Config::get('api.mail.internal-error.from_name');
        $this->internal_error = $internal_error;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->api_from_mail, $this->api_from_name)->
            view('mail.internal-error')->
            subject('Costs to Expect API: Internal error');
    }
}
