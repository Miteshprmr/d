<?php

namespace App\Api\V1\Http\Requests;

use Illuminate\Validation\Rule;

class AccountRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'balance' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/|max:99999999',
            'account_number' => [ 'required', 'max:255', Rule::unique('bank_accounts'), ],
        ];
    }
}
