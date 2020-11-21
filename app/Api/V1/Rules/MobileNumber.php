<?php

namespace App\Api\V1\Rules;

use Illuminate\Contracts\Validation\Rule;

class MobileNumber implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (! $value) {
            return true;
        }

        return (bool) preg_match('/^[0-9]{10}+$/', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.invalid_mobile');
    }
}
