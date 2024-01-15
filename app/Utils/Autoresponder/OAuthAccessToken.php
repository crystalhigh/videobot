<?php

namespace App\Utils\Autoresponder;

class OAuthAccessToken
{
    /**
     * The access token.
     *
     * @var string
     */
    protected $accessToken;

    /**
     * The refresh token.
     *
     * @var string|null
     */
    protected $refreshToken;

    /**
     * The access token expiry time in seconds.
     *
     * @var int|null
     */
    protected $expiresIn;

    /**
     * Create a new instance.
     *
     * @param  string  $accessToken
     * @param  string|null  $refreshToken
     * @param  int|null  $expiresIn
     * @return void
     */
    public function __construct(
        $accessToken,
        $refreshToken = null,
        $expiresIn = null
    ) {
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->expiresIn = $expiresIn;
    }

    /**
     * Get the access token.
     *
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Get the refresh token.
     *
     * @return string|null
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * Get the access token expiry time in seconds.
     *
     * @return int
     */
    public function getExpiresIn()
    {
        return $this->expiresIn;
    }
}
