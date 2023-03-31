<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OrderServices
{
    private static array $order_products = [];

    /**
     * @throws ValidationException
     */
    public static function createOrder(Request $request, $delivery_amount = 0, $delivery_provider = 'self'): Order
    {
        $amount = static::calculateOrder($request->products);

        $order =  Order::create([
            'amount' => $amount,
            'delivery_amount' => $delivery_amount,
            'total' => $amount + $delivery_amount,
            'deliver_provider' => $delivery_provider,
        ]);

        static::addOrderProducts($order);

        return $order;
    }

    /**
     * @throws ValidationException
     */
    private static function calculateOrder($products): float
    {
        $amount = 0;

        foreach ($products as $product) {
            $order_product = new OrderProductService($product);

            static::$order_products[] = $order_product;

            $amount += $order_product->total();
        }

        return $amount;
    }

    private static function addOrderProducts(Order $order): void
    {
        foreach (static::$order_products as $product) {
            $product->save($order);
        }
    }

}
