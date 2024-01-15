<?php

namespace App\Utils\Autoresponder;

interface WithOAuthDriverInterface
{
    /**
     * Get the access token.
     *
     * @param  string  $code The authorization code.
     * @return \App\Utils\Autoresponder\OAuthAccessToken
     */
    public function getAccessToken($code);

    /**
     * Renew the access token.
     *
     * @return \App\Utils\Autoresponder\OAuthAccessToken
     */
    public function renewAccessToken();

    /**
     * Get the authorization url.
     *
     * @return string
     */
    public function getAuthorizationUrl();

    /**
     * Revoke the access token.
     *
     * @return bool
     */
    public function unauthorize();

    /**
     * Check if the autoresponder requires OAuth 2.0
     *
     * @return bool
     */
    public function withOAuth();

    /**
     * Test the autoresponder's API with the current access token.
     *
     * If successful, it should returns the autoresponder's user account details.
     *
     * @return array
     */
    public function testApi();

    /**
     * Get the autoresponder's account.
     *
     * @return array|null
     */
    public function getAccount();

    /**
     * Get autoresponder's email lists.
     *
     * @return array An array of lists, otherwise an exception will be thrown.
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function getLists();

    /**
     * Add a new subscriber to the given autoresponder's email list.
     *
     * @param  string  $listId
     * @param  string  $email
     * @param  string  $firstName
     * @param  string  $lastName
     * @return bool True if the subscriber is added succesfully, otherwise an
     *     exception will be thrown.
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function addSubscriber(
        $listId,
        $email,
        $firstName = '',
        $lastName = ''
    );

    /**
     * Determine if the given response error is an Expired Token error.
     *
     * @param  mixed  $error
     * @return bool
     */
    public function isExpiredTokenError($error);
}
