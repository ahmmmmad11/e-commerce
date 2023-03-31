<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\ValidateProductRequest;
use App\Models\Category;
use App\Rules\ValidFile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class StoreProductRequest extends FormRequest
{
    use ValidateProductRequest;

    private Category|null $category;

    /**
     * Determine if the user is authorized to make this request.
     * @throws ValidationException
     */
    public function authorize(): bool
    {
        if (!$this->category = Category::find($this->category_id)) {
            throw ValidationException::withMessages(['category_id' => __('category id not found')]);
        }

        $this->validateCategoryRequiredProperties();
        $this->validateCategoryRestrictedProperties();

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
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'quantity' => ['required', 'numeric', 'min:1', 'max:1000'],
            'price' => ['required', 'numeric', 'min:1', 'max:1000000'],
            'image' => ['required', 'string', 'max:255', new ValidFile()],
            'images' => ['nullable', 'array'],
            'images.*' => ['required_with:images', 'string', new ValidFile()],
            'options' => ['required', 'array'],
            'options.*.name' => ['required', 'string', 'distinct'],
            'options.*.options' => ['required', 'array'],
            'options.*.options.*.value' => ['required', 'string'],
            'options.*.options.*.quantity' => ['required', 'digits_between:1,100'],
            'options.*.options.*.price' => ['nullable', 'numeric'],
        ];
    }
}
