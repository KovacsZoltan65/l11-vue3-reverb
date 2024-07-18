<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class NewUserCreated
 *
 * @package App\Events
 *
 * @property-read User $user
 */
class NewUserCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The user that was created
     *
     * @var User
     */
    public $user;

    /**
     * The channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('users');
    }

    /**
     * Create a new event instance.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    /*
     public function broadcastOn(): array
    {
        // The event should be broadcasted on a private channel named 'channel-name'.
        // The channel is created using the 'PrivateChannel' class.
        // This channel is used to notify the user about the newly created account.
        // The 'channel-name' is the name of the channel. Replace it with the actual name of the channel.
        return [
            new PrivateChannel('channel-name'),
        ];
    }
    */
}
