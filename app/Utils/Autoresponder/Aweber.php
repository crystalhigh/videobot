<?php

namespace App\Utils\Autoresponder;

class Aweber extends WithOAuthDriver
{
    /**
     * {@inheritdoc}
     */
    public function testApi()
    {
        return $this->getFirstAccount();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizationUrl()
    {
        return $this->oauthEndpoint .
            '/oauth2/authorize?client_id=' .
            urlencode($this->clientId) .
            '&scope=' .
            urlencode($this->scopes) .
            '&response_type=code';
    }

    /**
     * {@inheritdoc}
     */
    public function unauthorize()
    {
        return $this->httpRequest
            ->postAsForm(
                $this->oauthEndpoint . '/oauth2/revoke',
                [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'token' => $this->accessToken,
                    'token_type_hint' => 'access_token',
                ],
                [
                    'Authorization' =>
                        'Basic ' .
                        $this->makeBasicAuthentication(
                            $this->clientId,
                            $this->clientSecret
                        ),
                ]
            )
            ->ok();
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessToken($code)
    {
        $response = $this->httpRequest
            ->postAsForm(
                $this->oauthEndpoint . '/oauth2/token',
                [
                    'grant_type' => 'authorization_code',
                    'code' => $code,
                    'redirect_uri' => $this->redirectUri,
                ],
                [
                    'Authorization' =>
                        'Basic ' .
                        $this->makeBasicAuthentication(
                            $this->clientId,
                            $this->clientSecret
                        ),
                ]
            )
            ->json();

        return new OAuthAccessToken(
            array_key_exists('access_token', $response)
                ? $response['access_token']
                : null,
            array_key_exists('refresh_token', $response)
                ? $response['refresh_token']
                : null,
            array_key_exists('expires_in', $response)
                ? $response['expires_in']
                : null
        );
    }

    /**
     * {@inheritdoc}
     */
    public function renewAccessToken()
    {
        $response = $this->httpRequest
            ->postAsForm(
                $this->oauthEndpoint . '/oauth2/token',
                [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $this->refreshToken,
                    'redirect_uri' => $this->redirectUri,
                ],
                [
                    'Authorization' =>
                        'Basic ' .
                        $this->makeBasicAuthentication(
                            $this->clientId,
                            $this->clientSecret
                        ),
                ]
            )
            ->json();

        return new OAuthAccessToken(
            array_key_exists('access_token', $response)
                ? $response['access_token']
                : null,
            array_key_exists('refresh_token', $response)
                ? $response['refresh_token']
                : null,
            array_key_exists('expires_in', $response)
                ? $response['expires_in']
                : null
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAccount()
    {
        $account = $this->getFirstAccount();

        if (!is_array($account)) {
            return null;
        }

        return [
            // Unfortunately, Aweber doesn't give account email or name.
            'name' => (string) $account['id'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getLists()
    {
        $account = $this->getFirstAccount();

        if (!is_array($account)) {
            return [];
        }

        $accountId = $account['id'];
        $response = $this->handleRefreshToken(function () use ($accountId) {
            return $this->httpRequest
                ->get(
                    $this->endpoint . "/accounts/{$accountId}/lists",
                    [],
                    [
                        'Authorization' => 'Bearer ' . $this->accessToken,
                    ]
                )
                ->json();
        });

        $lists = [];

        if (
            is_array($response) &&
            array_key_exists('entries', $response) &&
            is_array($response['entries'])
        ) {
            foreach ($response['entries'] as $list) {
                array_push($lists, [
                    'id' => $list['id'],
                    'name' => $list['name'],
                ]);
            }
        }

        return $lists;
    }

    /**
     * {@inheritdoc}
     */
    public function addSubscriber(
        $listId,
        $email,
        $firstName = '',
        $lastName = ''
    ) {
        $account = $this->getFirstAccount();

        if (!is_array($account)) {
            return [];
        }

        $accountId = $account['id'];

        $this->handleRefreshToken(function () use (
            $accountId,
            $listId,
            $email,
            $firstName
        ) {
            return $this->httpRequest
                ->postAsForm(
                    $this->endpoint .
                        '/accounts/' .
                        $accountId .
                        '/lists/' .
                        $listId .
                        '/subscribers',
                    [
                        'email' => $email,
                        'name' => $firstName,
                    ],
                    [
                        'Authorization' => 'Bearer ' . $this->accessToken,
                    ]
                )
                ->json();
        });

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isExpiredTokenError($error)
    {
        return is_array($error) &&
            array_key_exists('error', $error) &&
            array_key_exists('error_description', $error) &&
            $error['error'] === 'invalid_token' &&
            strpos($error['error_description'], 'access token') !== false &&
            strpos($error['error_description'], 'expired') !== false;
    }

    /**
     * Get the first ever account in Aweber accounts list.
     *
     * @return array|null
     * @throws \Illuminate\Http\Client\RequestException
     */
    protected function getFirstAccount()
    {
        $response = $this->handleRefreshToken(function () {
            return $this->httpRequest
                ->get(
                    $this->endpoint . '/accounts',
                    [],
                    [
                        'Authorization' => 'Bearer ' . $this->accessToken,
                    ]
                )
                ->json();
        });

        if (
            is_array($response) &&
            array_key_exists('entries', $response) &&
            is_array($response['entries']) &&
            count($response['entries']) > 0
        ) {
            return $response['entries'][0];
        }

        return null;
    }
}
