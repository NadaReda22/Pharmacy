<?php

namespace App\Events;

// use Log;
use Illuminate\Support\Facades\Log;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;


class ProductBackInStock implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $product , $userId;

    /**
     * Create a new event instance.
     */
    public function __construct($product , $userId)
    {
        $this->product =$product;
        $this->userId =$userId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
public function broadcastOn(): array
{
   return [
        new PrivateChannel('user-' . $this->userId), // CHANGE IS HERE
    ];
}
        public function broadcastAs()
    {
        return 'ProductBackInStock';
    }

     // App\Events\ProductBackInStock.php
public function broadcastWith(): array
{
Log::info("Attempting to broadcast for User ID: {$this->userId}"); // TEMPORARY LOG
   
    return [
        'product_id'   => $this->product->id, 
        'product_name' => optional($this->product)->name, // Use optional() for safety
        'product_url'  => route('product.show', $this->product),
    ];
}

}
