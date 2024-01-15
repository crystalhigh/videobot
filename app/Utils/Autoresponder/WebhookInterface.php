<?php

namespace App\Utils\Autoresponder;

interface WebHookInterface
{
    /**
     * Set the WebHook URL.
     *
     * @param   string  $url The WebHook URL.
     * @return self
     */
    public function setUrl($url);

    /**
     * Send a subscriber's details to the WebHook.
     *
     * @param  string  $email The subscriber's email.
     * @param  string  $firstName The subscriber's first name.
     * @param  string  $lastName The subscriber's last name.
     * @return bool True if POST request is sent succesfully, otherwise an
     *     exception will be thrown.
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function post($email, $firstName = '', $lastName = '');
}
