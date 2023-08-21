<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResourceDeleted extends Notification implements ShouldQueue
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
            ->subject('C2E API: Resource Deleted')
            ->greeting('Hi,')
            ->line('A resource was successfully deleted, below is a summary of the deletes')
            ->line("Number of `data` rows deleted: " . $this->deletes['data'])
            ->line("Number of `log` rows deleted: " . $this->deletes['logs'])
            ->line("Number of `subcategory` rows deleted: " . $this->deletes['subcategories'])
            ->line("Number of `category` rows deleted: " . $this->deletes['categories'])
            ->line("Number of `transfer` rows deleted: " . $this->deletes['transfers'])
            ->line("Number of `partial transfer` rows deleted: " . $this->deletes['partial-transfers'])
            ->line("Number of `item data` rows deleted: " . $this->deletes['item-type-data'])
            ->line("Number of `item` rows deleted: " . $this->deletes['items'])
            ->line("Number of `resource item subtype` rows deleted: " . $this->deletes['resource-item-subtype'])
            ->line("Number of `resource` rows deleted: " . $this->deletes['resource']);
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
