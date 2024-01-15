<?php

namespace App\Utils\Autoresponder;

interface DriverInterface
{
    /**
     * Set the API key.
     *
     * @param   string  $apiKey The API key.
     * @return self
     */
    public function setApiKey($apiKey);

    /**
     * Check if the autoresponder is an OAuth 2.0 based driver.
     *
     * @return bool
     */
    public function withOAuth();

    /**
     * Test the autoresponder's API with the current API key.
     *
     * If successful, it should returns the autoresponder's user account details.
     *
     * @return array Account details, otherwise an exception will be thrown.
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function testApi();

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
}
