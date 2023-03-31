<?php

namespace App\Observers;


use App\Models\Order;

class OrderObserver
{
    /**
     * Handle the Product "creating" event.
     */
    public function creating(Order $order): void
    {
        $order->customer_id ??= auth()->user()->user->id;
        $order->delivery_provider ??= 'internally';
    }
}
