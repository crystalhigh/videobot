<?php

namespace App\Utils\Autoresponder;

class Mailchimp extends Driver implements MailchimpDriverInterface
{
    use Formattable;

    /**
     * The datacenter where MailChimp puts your account.
     *
     * @var string|null
     */
    protected $datacenter;

    /**
     * {@inheritdoc}
     */
    public function setDatacenter($datacenter)
    {
        $this->datacenter = $datacenter;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function testApi()
    {
        return $this->httpRequest
            ->get($this->getEndpoint(), null, [
                'Authorization' => 'Basic ' . $this->apiKey,
            ])
            ->json();
    }

    /**
     * {@inheritdoc}
     */
    public function getLists()
    {
        $response = $this->httpRequest
            ->get($this->getEndpoint() . '/lists', null, [
                'Authorization' => 'Basic ' . $this->apiKey,
            ])
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
            $this->getEndpoint() . "/lists/{$listId}",
            [
                'members' => [
                    [
                        'email_address' => $email,
                        'status' => 'subscribed',
                        'merge_fields' => [
                            'FNAME' => $this->formatName($firstName),
                            'LNAME' => $this->formatName($lastName),
                        ],
                    ],
                ],
            ],
            [
                'Authorization' => 'Basic ' . $this->apiKey,
            ]
        );

        return true;
    }

    /**
     * Get the Mailchimp endpoint with the user's datacenter.
     *
     * @return string
     */
    private function getEndpoint()
    {
        return str_replace('<dc>', $this->datacenter, $this->endpoint);
    }
}
