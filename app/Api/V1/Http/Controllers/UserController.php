<?php

namespace App\Api\V1\Http\Controllers;

use App\Models\User;
use App\Api\V1\Http\Resources\UserDetail;
use App\Api\V1\Http\Resources\UserCollection;

class UserController extends ApiController
{
    /**
     * Get users with bank accounts total balance.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $userQuery = User::orderBy('created_at', 'desc');

        $users = new UserCollection($userQuery->paginate());

        return $this->respondOk($users);
    }

    /**
     * Get user.
     *
     * @param User $user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(User $user)
    {
        $user = new UserDetail($user);

        return $this->respondOk($user);
    }

}
