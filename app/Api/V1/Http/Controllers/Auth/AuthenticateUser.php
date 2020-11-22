<?php

namespace App\Api\V1\Http\Controllers\Auth;

use Log;
use Auth;
use Hash;
use DateTime;
use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use GuzzleHttp\Psr7\Response;
use Laravel\Passport\Passport;
use Illuminate\Events\Dispatcher;
use League\OAuth2\Server\CryptKey;
use Laravel\Passport\Bridge\Client;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\Bridge\AccessToken;
use Illuminate\Auth\AuthenticationException;
use Psr\Http\Message\ServerRequestInterface;
use App\Exceptions\CustomValidationException;
use Laravel\Passport\Bridge\AccessTokenRepository;
use Laravel\Passport\Bridge\RefreshTokenRepository;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResponseTypes\BearerTokenResponse;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;

trait AuthenticateUser
{
    /**
     * Log the user in for web or mobile application.
     *
     * @param Request $request
     * @return User|\Illuminate\Http\Response
     * @throws CustomValidationException
     * @throws Exception
     */
    protected function loginUser(Request $request)
    {
        $user = $this->validateCredential($request);

        return $this->issueToken($request);
    }

    /**
     * Validate the user credential and return the user.
     *
     * @param Request $request
     * @return User
     * @throws CustomValidationException
     */
    protected function validateCredential(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');

        $isValidMobile = ! preg_match('/[^0-9]/', $username) && strlen((string) $username) === 10;
        $isValidEmail = filter_var($username, FILTER_VALIDATE_EMAIL) !== false;

        if (! $isValidMobile && ! $isValidEmail) {
            throw new CustomValidationException(['username' => __('validation.login_username_invalid')]);
        }

        $user = User::resolveUserFromUsername($username);

        if (! $user || ! Hash::check($password, $user->password)) {
            throw new CustomValidationException(['username' => __('auth.failed')]);
        }

        if (! $user->isActive()) {
            throw new CustomValidationException(['username' => __('auth.disabled')]);
        }

        return $user;
    }

    /**
     * Generate and issue the Password Grant Token for the user.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     * @throws CustomValidationException
     * @throws Exception
     */
    protected function issueToken(Request $request)
    {
        $clientCredentials = $this->getFirstPartyClientCredentials();
        $data = array_merge($clientCredentials, [
            'grant_type' => 'password',
            'username' => $request->input('username'),
            'password' => $request->input('password'),
        ]);

        $response = $this->issueTokenInternal($data);

        $this->validateAcccessTokenResponse($response);

        return $response;
    }

    /**
     * Generate and issue the Password Grant Token using the refresh token.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     * @throws AuthenticationException
     * @throws Exception
     */
    protected function issueTokenUsingRefreshToken(Request $request)
    {
        $clientCredentials = $this->getFirstPartyClientCredentials();

        $data = array_merge($clientCredentials, [
            'grant_type' => 'refresh_token',
            'refresh_token' => $request->input('refresh_token')
        ]);

        $response = $this->issueTokenInternal($data);

        $this->validateRefreshTokenResponse($response);

        return $response;
    }

    /**
     * Validate the login token response.
     *
     * @param \Illuminate\Http\Response $response
     * @throws CustomValidationException
     */
    protected function validateAcccessTokenResponse($response)
    {
        $content = $response->getContent();

        // Check if the error is due to invalid credentials.
        $isInvalidCredential = str_contains($content, 'invalid_credentials');

        if ($response->status() === 401 && $isInvalidCredential) {
            throw new CustomValidationException(['username' => __('auth.failed')]);
        }

        if ($response->status() !== 200) {
            Log::error("Token generation failed. [{$content}]");

            throw new CustomValidationException(['username' => __('auth.server_error')]);
        }
    }

    /**
     * Validate the refresh token response.
     *
     * @param \Illuminate\Http\Response $response
     * @throws AuthenticationException
     * @throws Exception
     */
    protected function validateRefreshTokenResponse($response)
    {
        $content = $response->getContent();

        // Check if the error is due to invalid or expired refresh token.
        $isInvalidToken = str_contains($content, 'invalid_request');

        if ($response->status() === 401 && $isInvalidToken) {
            throw new AuthenticationException(__('auth.invalid_refresh_token'));
        }

        if ($response->status() !== 200) {
            Log::error("Token generation failed (Using refresh token). [{$content}]");

            throw new Exception(__('auth.server_error'));
        }
    }

    /**
     * Issue the token internally by calling the Passport methods.
     *
     * @param array $data
     * @return \Illuminate\Http\Response
     */
    protected function issueTokenInternal(array $data)
    {
        $parsedData = app(ServerRequestInterface::class)->withParsedBody($data);

        return app(AccessTokenController::class)->issueToken($parsedData);
    }

    /**
     * Get the first party client credentials used for generation access tokens.
     *
     * @return array
     * @throws Exception
     */
    protected function getFirstPartyClientCredentials()
    {
        $clientId = config('passport.first_party_app.client_id');
        $clientSecret = config('passport.first_party_app.client_secret');

        if (! $clientId || ! $clientSecret) {
            throw new Exception(__('errors.invalid_client_id_secret'));
        }

        return [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
        ];
    }

    /**
     * @param \App\Entities\User $user
     * @param $clientId
     * @param bool $output default = true
     * @return array | \League\OAuth2\Server\ResponseTypes\BearerTokenResponse
     */
    protected function getBearerTokenByUser(User $user, $clientId, $output = true)
    {
        $passportToken = $this->createPassportTokenByUser($user, $clientId);
        $bearerToken = $this->sendBearerTokenResponse($passportToken['access_token'], $passportToken['refresh_token']);

        if (! $output) {
            $bearerToken = json_decode($bearerToken->getBody()->__toString(), true);
        }

        return $bearerToken;
    }

    protected function createPassportTokenByUser(User $user, $clientId)
    {
        $accessToken = new AccessToken($user->id);
        $accessToken->setIdentifier($this->generateUniqueIdentifier());
        $accessToken->setClient(new Client($clientId, null, null));
        $accessToken->setExpiryDateTime((new DateTime())->add(Passport::tokensExpireIn()));

        $accessTokenRepository = new AccessTokenRepository(new TokenRepository(), new Dispatcher());
        $accessTokenRepository->persistNewAccessToken($accessToken);
        $refreshToken = $this->issueRefreshToken($accessToken);

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
        ];
    }

    protected function sendBearerTokenResponse($accessToken, $refreshToken)
    {
        $response = new BearerTokenResponse();
        $response->setAccessToken($accessToken);
        $response->setRefreshToken($refreshToken);

        $privateKey = new CryptKey('file://'.Passport::keyPath('oauth-private.key'));

        $response->setPrivateKey($privateKey);
        $response->setEncryptionKey(app('encrypter')->getKey());

        return $response->generateHttpResponse(new Response);
    }
    /**
     * Generate a new unique identifier.
     *
     * @param int $length
     *
     * @throws OAuthServerException
     *
     * @return string
     */
    private function generateUniqueIdentifier($length = 40)
    {
        try {
            return bin2hex(random_bytes($length));
            // @codeCoverageIgnoreStart
        } catch (\TypeError $e) {
            throw OAuthServerException::serverError('An unexpected error has occurred');
        } catch (\Error $e) {
            throw OAuthServerException::serverError('An unexpected error has occurred');
        } catch (\Exception $e) {
            // If you get this message, the CSPRNG failed hard.
            throw OAuthServerException::serverError('Could not generate a random string');
        }
        // @codeCoverageIgnoreEnd
    }
    private function issueRefreshToken(AccessTokenEntityInterface $accessToken)
    {
        $maxGenerationAttempts = 10;
        $refreshTokenRepository = app(RefreshTokenRepository::class);

        $refreshToken = $refreshTokenRepository->getNewRefreshToken();
        $refreshToken->setExpiryDateTime((new \DateTime())->add(Passport::refreshTokensExpireIn()));
        $refreshToken->setAccessToken($accessToken);

        while ($maxGenerationAttempts-- > 0) {
            $refreshToken->setIdentifier($this->generateUniqueIdentifier());
            try {
                $refreshTokenRepository->persistNewRefreshToken($refreshToken);

                return $refreshToken;
            } catch (UniqueTokenIdentifierConstraintViolationException $e) {
                if ($maxGenerationAttempts === 0) {
                    throw $e;
                }
            }
        }
    }
}
