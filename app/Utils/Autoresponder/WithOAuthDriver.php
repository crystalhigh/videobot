<?php

namespace App\Utils\Autoresponder;

use Illuminate\Http\Client\RequestException;
use ReflectionClass;
use RuntimeException;
use Throwable;

abstract class WithOAuthDriver implements WithOAuthDriverInterface
{
    /**
     * The HttpRequest instance.
     *
     * @var \App\Utils\Autoresponder\HttpRequest
     */
    protected $httpRequest;

    /**
     * The access token.
     *
     * @var string|null
     */
    protected $accessToken;

    /**
     * The refresh token.
     *
     * @var string|null
     */
    protected $refreshToken;

    /**
     * The token expiry in seconds.
     *
     * @var int|null
     */
    protected $expiresIn;

    /**
     * The autoresponder API endpoint.
     *
     * @var string
     */
    protected $endpoint;

    /**
     * The autoresponder OAuth API endpoint.
     *
     * @var string
     */
    protected $oauthEndpoint;

    /**
     * The autoresponder client id.
     *
     * @var string
     */
    protected $clientId;

    /**
     * The autoresponder client secret.
     *
     * @var string
     */
    protected $clientSecret;

    /**
     * The autoresponder OAuth redirect uri.
     *
     * @var string
     */
    protected $redirectUri;

    /**
     * The autoresponder API scopes.
     *
     * @var string
     */
    protected $scopes;

    /**
     * Determines if the initial given access token was modified.
     *
     * This is very useful to update the user's autoresponder access token
     * in the database.
     *
     * @var bool
     */
    private $isDirtyAccessToken = false;

    /**
     * Create a new driver instance.
     *
     * @return void
     */
    public function __construct()
    {
        $name = strtolower((new ReflectionClass($this))->getShortName());

        $this->httpRequest = new HttpRequest();
        $this->endpoint = config('autoresponder.' . $name . '.endpoint');
        $this->oauthEndpoint = config(
            'autoresponder.' . $name . '.oauth_endpoint'
        );
        $this->scopes = config('autoresponder.' . $name . '.scopes');
        $this->clientId = config('autoresponder.' . $name . '.client_id');
        $this->clientSecret = config(
            'autoresponder.' . $name . '.client_secret'
        );
        $this->redirectUri = config('autoresponder.' . $name . '.redirect_uri');
    }

    /**
     * Set the access token.
     *
     * @param   string|null  $accessToken The access token.
     * @return self
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * Get the fresh access token.
     *
     * @return null|\App\Utils\Autoresponder\OAuthAccessToken
     */
    public function getFreshAccessToken()
    {
        if (!$this->isDirtyAccessToken) {
            return null;
        }

        return new OAuthAccessToken(
            $this->accessToken,
            $this->refreshToken,
            $this->expiresIn
        );
    }

    /**
     * Set the refresh token.
     *
     * @param   string|null  $refreshToken The refresh token.
     * @return self
     */
    public function setRefreshToken($refreshToken)
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }

    /**
     * Set the token expiry in seconds.
     *
     * @param   int|null  $expiresIn The token expiry in seconds.
     * @return self
     */
    public function setExpiresIn($expiresIn)
    {
        $this->expiresIn = $expiresIn;

        return $this;
    }

    /**
     * Mark the access token as dirty if the initial given access token was
     * refresh during a HTTP request to the autorespoder due to an Expired
     * Token error.
     *
     * @return $this
     */
    public function markAccessTokenAsDirty()
    {
        $this->isDirtyAccessToken = true;

        return $this;
    }

    /**
     * Determines if the initial given access token was modified.
     *
     * @return bool
     */
    public function dirtyAccessToken()
    {
        return $this->isDirtyAccessToken;
    }

    /**
     * {@inheritdoc}
     */
    public function withOAuth()
    {
        return true;
    }

    /**
     * Call the given closure and handle if there's any Expired Token
     * in the response error.
     *
     * @param  \Closure  $closure
     * @return void
     */
    protected function handleRefreshToken($closure)
    {
        $shouldRun = true;
        $numberOfTries = 1;
        $response = null;

        while ($shouldRun) {
            try {
                $response = $closure();
                $shouldRun = false;
            } catch (Throwable $e) {
                if (
                    $e instanceof RequestException &&
                    $this->isExpiredTokenError($e->response->json())
                ) {
                    if ($numberOfTries > 1) {
                        throw new RuntimeException(
                            "Can't retry to refresh the autoresponder's token for more than 1 time."
                        );
                    }

                    $numberOfTries += 1;
                    $freshAccessToken = $this->renewAccessToken();

                    $this->setAccessToken($freshAccessToken->getAccessToken())
                        ->setRefreshToken($freshAccessToken->getRefreshToken())
                        ->setExpiresIn($freshAccessToken->getExpiresIn())
                        ->markAccessTokenAsDirty();
                } else {
                    throw $e;
                }
            }
        }

        return $response;
    }

    /**
     * Make an OAuth basic authentication.
     *
     * @param  string  $username
     * @param  string  $password
     * @return string
     */
    protected function makeBasicAuthentication($username, $password)
    {
        return base64_encode($username . ':' . $password);
    }
}
