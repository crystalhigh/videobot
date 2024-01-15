<?php

namespace App\Utils\Autoresponder;

use Illuminate\Support\Facades\Http;

class HttpRequest
{
    /**
     * The request timeout in seconds.
     *
     * @var int
     */
    protected $timeout = 3;

    /**
     * Make a GET HTTP request.
     *
     * @param  string  $endpoint
     * @param array $data
     * @param array|null $headers
     * @return \Illuminate\Http\Client\Response
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function get($endpoint, $data = [], $headers = null)
    {
        return $this->call($endpoint, false, false, false, $data, $headers);
    }

    /**
     * Make a JSON GET HTTP request.
     *
     * @param  string  $endpoint
     * @param array $data
     * @param array|null $headers
     * @return \Illuminate\Http\Client\Response
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function getAsJson($endpoint, $data = [], $headers = null)
    {
        return $this->call($endpoint, false, false, true, $data, $headers);
    }

    /**
     * Make a JSON POST HTTP request.
     *
     * @param  string  $endpoint
     * @param array $data
     * @param array|null $headers
     * @return \Illuminate\Http\Client\Response
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function postAsJson($endpoint, $data = [], $headers = null)
    {
        return $this->call($endpoint, true, false, true, $data, $headers);
    }

    /**
     * Make a POST HTTP request as form.
     *
     * @param  string  $endpoint
     * @param array $data
     * @param array|null $headers
     * @return \Illuminate\Http\Client\Response
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function postAsForm($endpoint, $data = [], $headers = null)
    {
        return $this->call($endpoint, true, true, false, $data, $headers);
    }

    /**
     * Make an HTTP request.
     *
     * @param  string  $endpoint
     * @param  bool  $isPost
     * @param  bool  $asForm
     * @param array $data
     * @param array|null $headers
     * @return \Illuminate\Http\Client\Response
     * @throws \Illuminate\Http\Client\RequestException
     */
    protected function call(
        $endpoint,
        $isPost = false,
        $asForm = false,
        $asJson = false,
        $data = [],
        $headers = null
    ) {
        $http = Http::timeout($this->timeout);

        if ($headers) {
            $http->withHeaders($headers);
        }

        if ($asForm) {
            $http->asForm();
        }

        if ($asJson) {
            $http->acceptJson()->asJson();
        }

        $response = $http->{$isPost ? 'post' : 'get'}($endpoint, $data);

        $response->throw();

        return $response;
    }
}
