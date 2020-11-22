<?php

namespace App\Api\V1\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Validation\Rule;
use App\Api\V1\Rules\MobileNumber;
use Illuminate\Validation\Validator;
use App\Api\V1\Http\Requests\BaseRequest;

class RegistrationRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => [ 'required', 'string', 'min:3', 'max:50', ],
            'last_name' => [ 'required', 'string', 'min:3', 'max:50', ],
            'email' => [ 'required', 'email', Rule::unique('users'), ],
            'mobile' => ['required', new MobileNumber, Rule::unique('users'), ],
            'password' => [ 'required', 'string', 'min:6', 'confirmed', ],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param Validator $validator
     * @return void
     */
    public function withValidator(Validator $validator)
    {
        $validator->after(function (Validator $validator) {
        });
    }
}
