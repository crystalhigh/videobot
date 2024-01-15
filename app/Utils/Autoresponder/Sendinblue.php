<?php

namespace App\Utils\Autoresponder;

class Sendinblue extends Driver
{
    use Formattable;

    /**
     * {@inheritdoc}
     */
    public function testApi()
    {
        return $this->httpRequest
            ->get($this->endpoint . '/account', null, [
                'api-key' => $this->apiKey,
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
                $this->endpoint . '/contacts/lists',
                [],
                [
                    'api-key' => $this->apiKey,
                ]
            )
            ->json();

        $lists = [];

        if (
            is_array($response) &&
            array_key_exists('lists', $response) &&
            is_array($response['lists'])
        ) {
            foreach ($response['lists'] as $list) {
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
        $this->httpRequest->postAsJson(
            $this->endpoint . '/contacts',
            [
                'listIds' => [$listId],
                'email' => $email,
                'attributes' => [
                    'FIRSTNAME' => $this->formatName($firstName),
                    'LASTNAME' => $this->formatName($lastName),
                ],
            ],
            [
                'api-key' => $this->apiKey,
            ]
        );

        return true;
    }
}
