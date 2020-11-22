<?php

namespace App\Api\V1\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AccountDetail extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'balance' => $this->balance,
            'account_number' => $this->account_number,
        ];
    }
}
