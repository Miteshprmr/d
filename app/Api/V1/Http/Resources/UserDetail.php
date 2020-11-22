<?php

namespace App\Api\V1\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserDetail extends JsonResource
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
            'name' => $this->name,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'balance' => $this->bankAccounts->sum('balance')
        ];
    }
}
