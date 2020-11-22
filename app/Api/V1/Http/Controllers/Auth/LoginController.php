<?php

namespace App\Api\V1\Http\Controllers\Auth;

use App\Api\V1\Http\Controllers\ApiController;
use App\Api\V1\Http\Requests\Auth\LoginRequest;
use App\Api\V1\Http\Requests\Auth\RefreshTokenRequest;
use App\Api\V1\Http\Requests\Auth\ClientCredentialTokenRequest;

class LoginController extends ApiController
{
    use AuthenticateUser;

    /**
     * Handle the login request and generate the access token.
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\Response
     * @throws \App\Exceptions\CustomValidationException
     * @throws \Exception
     */
    public function login(LoginRequest $request)
    {
        return $this->loginUser($request);
    }

    /**
     * Generate the new access token using the refresh token.
     *
     * @param RefreshTokenRequest $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\AuthenticationException
     * @throws \Exception
     */
    public function refreshToken(RefreshTokenRequest $request)
    {
        return $this->issueTokenUsingRefreshToken($request);
    }

}
