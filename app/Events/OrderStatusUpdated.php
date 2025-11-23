<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;

class OrderStatusUpdated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public int $orderId;
    public string $status;
    public ?string $message;
    public string $updatedAt;

    public function __construct(Order $order, ?string $message = null)
    {
        $this->orderId = $order->id;
        $this->status = $order->status;
        $this->message = $message;
        $this->updatedAt = now()->toIso8601String();
    }


    public function broadcastOn()
    {
        return new Channel("order.{$this->orderId}");
    }


    public function broadcastAs()
    {

        return 'OrderStatusUpdated';
    }


}
