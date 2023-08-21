<?php

namespace App\Notifications;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ForgotPassword extends Notification implements ShouldQueue
{
    use Queueable;

    private User $user;
    private string $token;

    public function __construct($user, string $token)
    {
        $this->user = $user;
        $this->token = $token;
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
        return (new MailMessage())
            ->subject('Costs to Expect API: Reset password')
            ->greeting('Hi,')
            ->line('We have received a request to reset your password, if this was you please follow the step below, if this was not you, please ignore this email and let us know.')
            ->line("To create a new password please POST `password` and `password_confirmation` to " . url('/v3/auth/create-new-password?email=' . urlencode($this->user->email) . '&token=' . urlencode($this->token)))
            ->line('Thank you for using the Costs to Expect API.');
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
