<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Validation\ValidationException;

class OrderProductService
{
    private Product $product;
    private array $order_item;

    /**
     * @throws ValidationException
     */
    public function __construct(array $item)
    {
        $this->product = Product::findOrFail($item['id']);
        $this->order_item = $item;

        $this->validate();
        $this->applyCoupon();
    }

    /**
     * @throws ValidationException
     */
    private function validate(): void
    {
        $this->getPrice();
        $this->inStock();
        $this->addOptionsPrices();
    }

    /**
     * @throws ValidationException
     */
    private function inStock(): void
    {
        if (!$this->product->quantity > $this->order_item['quantity']) {
            throw ValidationException::withMessages([__($this->product->name) => __('is out of stock')]);
        }
    }

    /**
     * @throws ValidationException
     */
    private function optionIsAvailable($name, $value): array
    {
        if(!$product_option = $this->product->options->where('name', $name)
            ->pluck('options')
            ->flatten(1)
            ->where('value', $value)
            ->where('quantity', '>', $this->order_item['quantity'])
            ->first()) {
            throw ValidationException::withMessages([__($this->product->name) => __("$value option is out of stock")]);
        }

        return $product_option;
    }

    private function getPrice(): void
    {
        $this->order_item['price'] = $this->product->price;
        $this->order_item['total'] = $this->product->price;
    }

    /**
     * @throws ValidationException
     */
    public function addOptionsPrices(): void
    {
        $this->order_item['options_value'] = 0;

        foreach ($this->order_item['options'] as $name => $value) {
            if ($product_option = $this->optionIsAvailable($name, $value)) {
                $this->order_item['total'] += $product_option['price'] ?? 0;
                $this->order_item['options_value'] += $product_option['price'] ?? 0;
            }
        }
    }

    /**
     * @throws ValidationException
     */
    private function applyCoupon(): void
    {
        if (key_exists('coupon', $this->order_item)) {
            $coupon_value = (new ApplyCouponService($this->order_item['coupon'], $this->product))->value;
            $this->order_item['total'] -= $coupon_value;
            $this->order_item['coupon_value'] = $coupon_value;
        }
    }

    private function updateProduct(): void
    {
        $this->product->updateQuantities($this->order_item['quantity'], $this->order_item['options']);
    }

    public function total(): float
    {
        return $this->order_item['total'] *= $this->order_item['quantity'];
    }

    public function save(Order $order): void
    {
        $order->products()->create([
            'product_id' => $this->order_item['id'],
            'quantity' => $this->order_item['quantity'],
            'options' => $this->order_item['options'],
            'price' => $this->order_item['price'],
            'total' => $this->order_item['total'],
            'coupon' => $this->order_item['coupon'] ?? null,
            'coupon_value' => $this->order_item['coupon_value'] ?? 0,
            'options_value' => $this->order_item['options_value'] ?? 0
        ]);

        $this->updateProduct();
    }
}
