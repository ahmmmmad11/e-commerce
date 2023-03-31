<?php

namespace App\Http\Requests;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\Seller;
use Illuminate\Foundation\Http\FormRequest;

class CouponProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth()->user();


        if ($user->user_type == Seller::class && $this->coupon_id) {
            $coupon = Coupon::find($this->coupon_id);

            // abort if the coupon does not belong to the authenticated seller
            abort_if($coupon->seller_id !== $user->user_id, 403);

            $products = Product::where('seller_id', $user->user_id)->whereIn('id', $this->products)->get();
            // abort if a product is not belongs to the authenticated seller
            abort_if($products->pluck('id')->toArray() != $this->products, 403);

            if ($coupon->type == 'value' && $products->contains(function ($item, $key) use ($coupon){
                    return $item->price < $coupon->amount;
                })
            ) {
                abort(422, __('all products prices\' should be less than coupon amount'));
            }
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'coupon_id' => ['required', 'exists:coupons,id'],
            'products' => ['required', 'array'],
            'products.*' => ['exists:products,id']
        ];
    }
}
