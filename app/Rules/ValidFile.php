<?php

namespace App\Rules;

use App\Models\File;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidFile implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!File::where('name', $value)->first()) {
            $fail('The :attribute must be existed on the system.');
        }
    }
}
