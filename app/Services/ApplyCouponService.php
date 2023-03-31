<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Validation\ValidationException;

class ApplyCouponService
{
    private Coupon $coupon;
    private Product $product;
    public int|float $value;

    /**
     * @throws ValidationException
     */
    public function __construct(string $coupon, Product $product)
    {
        $this->coupon = Coupon::where('coupon', $coupon)->first();

        $this->product = $product;

        $this->value = $this->getCouponValue();
    }

    /**
     * @throws ValidationException
     */
    private function couponIsActive(): void
    {
        if ($this->coupon->status !== 'active') {
            throw ValidationException::withMessages(['coupon' => __("{$this->coupon->coupon} is expired")]);
        }
    }

    /**
     * @throws ValidationException
     */
    private function isValidCoupon(): void
    {
        if ($this->coupon->end_at < now()) {
            throw ValidationException::withMessages(['coupon' => __("{$this->coupon->coupon} is expired")]);
        }
    }

    /**
     * @throws ValidationException
     */
    private function couponBelongsToProduct(): void
    {
        if ($this->coupon->seller_id && $this->coupon->seller_id !== $this->product->seller_id) {
            throw ValidationException::withMessages(['coupon' => __("un valid coupon")]);
        }

        if (count($this->coupon->products) && !$this->coupon->products->pluck('id')->contains($this->product->id)) {
            throw ValidationException::withMessages(['coupon' => __("un valid coupon")]);
        }
    }

    /**
     * @throws ValidationException
     */
    private function validateCoupon(): void
    {
        $this->couponIsActive();

        $this->isValidCoupon();

        $this->couponBelongsToProduct();
    }

    /**
     * @throws ValidationException
     */
    protected function getCouponValue()
    {
        $this->validateCoupon();

        if ($this->coupon->type === 'value') {
            return $this->coupon->amount;
        }

        return (float) ($this->coupon->amount / 100) * (float) $this->product->price;
    }
}
