<?php

namespace App\Api\V1\Http\Controllers;

use Exception;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Helpers\Response\ResponseHelpers;
use App\Api\V1\Http\Resources\User as UserResource;

abstract class ApiController extends Controller
{
    use ResponseHelpers;

    /**
     * The authenticated user.
     *
     * @var \App\Models\User
     */
    protected $user;

    /**
     * Get the currently logged in user.
     *
     * @return \App\Models\User|null
     */
    protected function getLoggedInUser()
    {
        return auth()->user();
    }

    /**
     * Transform the user object.
     *
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    protected function transformUser(User $user)
    {
        return $this->respondOk(new UserResource($user));
    }

    /**
     * Log the exception.
     *
     * @param Exception $exception
     */
    protected function logException(Exception $exception)
    {
        log_exception($exception);
    }
}
