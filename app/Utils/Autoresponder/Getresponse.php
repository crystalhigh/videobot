<?php

namespace App\Utils\Autoresponder;

class Getresponse extends Driver
{
    use Formattable;

    /**
     * {@inheritdoc}
     */
    public function testApi()
    {
        return $this->httpRequest
            ->get($this->endpoint . '/accounts', null, [
                'X-Auth-Token' => 'api-key ' . $this->apiKey,
            ])
            ->json();
    }

    /**
     * {@inheritdoc}
     */
    public function getLists()
    {
        $response = $this->httpRequest
            ->get(
                $this->endpoint . '/campaigns',
                [],
                [
                    'X-Auth-Token' => 'api-key ' . $this->apiKey,
                ]
            )
            ->json();

        $lists = [];

        if (!is_null($response)) {
            foreach ($response as $list) {
                array_push($lists, [
                    'id' => $list['campaignId'],
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
        $this->httpRequest->postAsJson(
            $this->endpoint . '/contacts',
            array_merge(
                [
                    'campaign' => [
                        'campaignId' => $listId,
                    ],
                    'email' => $email,
                ],
                $firstName || $lastName
                    ? ['name' => $this->makeFullname($firstName, $lastName)]
                    : []
            ),
            [
                'X-Auth-Token' => 'api-key ' . $this->apiKey,
            ]
        );

        return true;
    }
}
