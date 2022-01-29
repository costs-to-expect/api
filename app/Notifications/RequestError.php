<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequestError extends Notification implements ShouldQueue
{
    use Queueable;

    private array $error;

    public function __construct(array $error)
    {
        $this->error = $error;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Costs to Expect API: Request Error')
            ->greeting('Hi,')
            ->line('There was a request error, details below')
            ->line("Method: " . $this->error['method'])
            ->line("Expected status code: " . $this->error['expected_status_code'])
            ->line("Returned status code: " . $this->error['returned_status_code'])
            ->line("Requested URI: " . $this->error['request_uri'])
            ->line("Source: " . $this->error['source'])
            ->line("Referer: " . $this->error['referer'])
            ->line("Debug: " . $this->error['debug']);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
