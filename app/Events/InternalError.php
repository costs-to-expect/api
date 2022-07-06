<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class InternalError
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $internal_error = [];

    /**
     * Create a new event instance.
     *
     * @param array $internal_error
     *
     * @return void
     */
    public function __construct(array $internal_error)
    {
        $this->internal_error = $internal_error;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
