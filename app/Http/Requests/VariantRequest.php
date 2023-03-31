<?php

namespace App\Http\Requests;

use App\Models\Product;
use App\Models\Seller;
use Illuminate\Foundation\Http\FormRequest;

class VariantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth()->user();

        if ($user->user_type == Seller::class && $this->product_id) {
            $product = Product::find($this->product_id);

            // abort if the product does not belong to the authenticated seller
            abort_if($product->seller_id !== $user->user_id, 403);

            $variants = Product::where('seller_id', $user->user_id)->whereIn($this->variants)->pluck('id');

            // abort if a variant is not belongs to the authenticated seller
            abort_if($variants !== $this->variants, 403);
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
            'product_id' => ['required', 'exists:products,id'],
            'variants' => ['required', 'array'],
            'variants.*' => ['required', 'exists:products,id'],
        ];
    }
}
