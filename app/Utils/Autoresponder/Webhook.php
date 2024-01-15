<?php

namespace App\Utils\Autoresponder;

class Webhook implements WebhookInterface
{
    use Formattable;

    /**
     * The HttpRequest instance.
     *
     * @var \App\Utils\Autoresponder\HttpRequest
     */
    protected $httpRequest;

    /**
     * The WebHook URL.
     *
     * @var string
     */
    protected $url;

    /**
     * Create a new WebHook instance.
     *
     * @param  string  $url The WebHook URL.
     * @return void
     */
    public function __construct($url)
    {
        $this->httpRequest = new HttpRequest();
        $this->url = $url;
    }

    /**
     * {@inheritdoc}
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function post($email, $firstName = '', $lastName = '')
    {
        $this->httpRequest->postAsJson($this->url, [
            'email' => $email,
            'first_name' => $this->formatName($firstName),
            'last_name' => $this->formatName($lastName),
        ]);

        return true;
    }
}
