<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResourceTypeDeleted extends Notification implements ShouldQueue
{
    use Queueable;

    private array $deletes;

    public function __construct(array $deletes)
    {
        $this->deletes = $deletes;
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
            ->subject('Costs to Expect API: Resource Type Deleted')
            ->greeting('Hi,')
            ->line('A resource type was successfully deleted, details below')
            ->line("Number of `subcategory` rows deleted: " . $this->deletes['subcategories'])
            ->line("Number of `category` rows deleted: " . $this->deletes['categories'])
            ->line("Number of `resource type item type` rows deleted: " . $this->deletes['resource-type-item-type'])
            ->line("Number of `permitted user` rows deleted: " . $this->deletes['permitted-users'])
            ->line("Number of `resource type` rows deleted: " . $this->deletes['resource-type']);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return $this->deletes;
    }
}
