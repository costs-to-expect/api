<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FailedJob extends Notification implements ShouldQueue
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
            ->subject('Costs to Expect API: Failed job')
            ->greeting('Hi,')
            ->line('A `ClearCache` job failed to run, details below')
            ->line("Message: " . $this->error['message'])
            ->line("File: " . $this->error['file'])
            ->line("Line: " . $this->error['line'])
            ->line("Trace: " . $this->error['trace']);
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
