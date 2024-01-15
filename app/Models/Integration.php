<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;

/**
 * @property-read string $accessToken
 * @property-read string $refreshToken
 * @property-read string $accessTokenExpiresIn
 * @property-read string $accessTokenAccountName
 */
class Integration extends Model
{
    use Uuid;
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];

    protected $casts = [
        'oauth_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Return the access token.
     *
     * @return string|null
     */
    public function getAccessTokenAttribute()
    {
        return $this->getElementFromOAuthData('access_token');
    }

    /**
     * Return the refresh token.
     *
     * @return string|null
     */
    public function getRefreshTokenAttribute()
    {
        return $this->getElementFromOAuthData('refresh_token');
    }

    /**
     * Return the access token expiry duration in seconds.
     *
     * @return string|null
     */
    public function getAccessTokenExpiresInAttribute()
    {
        return $this->getElementFromOAuthData('expires_in');
    }

    /**
     * Return the access token account name.
     *
     * @return string|null
     */
    public function getAccessTokenAccountNameAttribute()
    {
        return $this->getElementFromOAuthData('name');
    }

    /**
     * Get an element from the oauth_data array.
     *
     * @param  string  $key
     * @return string|null
     */
    protected function getElementFromOAuthData($key)
    {
        if (
            !is_array($this->oauth_data) ||
            !array_key_exists($key, $this->oauth_data)
        ) {
            return null;
        }

        return $this->oauth_data[$key];
    }
}
