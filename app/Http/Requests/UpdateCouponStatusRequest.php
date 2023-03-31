<?php

namespace App\Http\Requests;

use App\Models\Seller;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCouponStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth()->user();

        if ($user->user_type == Seller::class) {
            $coupon = $this->route()->parameter('coupon');

            // abort if the coupon does not belong to the authenticated seller
            abort_if($coupon->seller_id !== $user->user_id, 403);

            abort_if(
                (
                    $coupon->status == 'disabled' &&
                    $this->status == 'active' &&
                    now() > Carbon::parse($coupon->end_at)
                ),
                403,
                __('you cannot activated coupon if it\'s end date is expired')
            );
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
            'status' => ['required', 'in:active,disabled']
        ];
    }
}
