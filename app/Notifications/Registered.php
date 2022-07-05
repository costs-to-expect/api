<?php

namespace App\Notifications;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Registered extends Notification implements ShouldQueue
{
    use Queueable;

    private User $user;
    private string $token;

    public function __construct(User $user, string $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return
            (new MailMessage())
                ->subject('Costs to Expect API: Account registration')
                ->greeting('Hi,')
                ->line('Thank you for registering with the Costs to Expect API.')
                ->line('To use the Costs to Expect API you need to complete your account. To complete your account you need to create a password.')
                ->line("To create a password please POST `password` and `password_confirmation` to " . url('/v2/auth/create-password?email=' . urlencode($this->user->email) . '&token=' . urlencode($this->token)))
                ->line('Whilst we have you, the documentation for the API is available online.')
                ->action('Read our documentation', url('https://postman.costs-to-expect.com/'))
                ->line('Thank you for using the Costs to Expect API.');
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
