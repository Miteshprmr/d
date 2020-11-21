<?php

namespace App\Api\V1\Http\Controllers\Auth;

use App\Api\V1\Http\Controllers\ApiController;

class SessionController extends ApiController
{
    use HandleUserSessions;

    /**
     * SessionController constructor.
     */
    public function __construct()
    {
        $this->user = $this->getLoggedInUser();
    }

    /**
     * Logout the user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        if ($token = $this->user->token()) {
            $this->revokeAccessToken($token);
        }

        return $this->respondSuccess();
    }
}
