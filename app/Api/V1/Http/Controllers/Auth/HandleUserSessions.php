<?php

namespace App\Api\V1\Http\Controllers\Auth;

use DB;
use Laravel\Passport\Token;

trait HandleUserSessions
{
    /**
     * Revoke the user's access token with it's refresh token.
     *
     * @param Token $token
     */
    protected function revokeAccessToken(Token $token)
    {
        // Revoke the refresh token associated with the access token.
        DB::table('oauth_refresh_tokens')
            ->where('access_token_id', $token->id)
            ->update(['revoked' => true]);

        // Revoke the access token.
        $token->revoke();
    }
}
