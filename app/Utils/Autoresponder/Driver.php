<?php

namespace App\Utils\Autoresponder;

use ReflectionClass;

abstract class Driver implements DriverInterface
{
    /**
     * The HttpRequest instance.
     *
     * @var \App\Utils\Autoresponder\HttpRequest
     */
    protected $httpRequest;

    /**
     * The API key.
     *
     * @var string
     */
    protected $apiKey;

    /**
     * Aweber API endpoint.
     *
     * @var string
     */
    protected $endpoint;

    /**
     * Create a new driver instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->httpRequest = new HttpRequest();
        $this->endpoint = config(
            'autoresponder.' .
                strtolower((new ReflectionClass($this))->getShortName()) .
                '.endpoint'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function withOAuth()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }
}
