<?php

namespace App\Services;

use App\Models\Integration;
use App\Utils\Autoresponder\Autoresponder;
use App\Utils\Autoresponder\Mailchimp;
use App\Utils\Autoresponder\Webhook;
use Illuminate\Support\Facades\DB;

class AutoresponderService
{
    /**
     * Check if the given autoresponder is a WebHook.
     *
     * @param  string  $autoresponder
     * @return bool
     */
    public function isWebHook($autoresponder)
    {
        return in_array($autoresponder, ['zapier', 'pabbly']);
    }

    /**
     * Get the autoresponder lists of the user.
     *
     * @param  int  $userId
     * @param  string  $autoresponder
     * @return array
     */
    public function getListsOfUser($userId, $autoresponder)
    {
        if ($this->isWebHook($autoresponder)) {
            throw new AutoresponderServiceException(
                printf("%s doesn't work with lists.", ucfirst($autoresponder)),
                400
            );
        }

        /** @var \App\Models\Integration|null */
        $integration = Integration::query()
            ->where('user_id', $userId)
            ->where('type', $autoresponder)
            ->first();

        if (is_null($integration)) {
            throw new AutoresponderServiceException(
                "The autoresponder doesn't exists.",
                400
            );
        }

        $autoresponderInstance = $this->createAutoresponderFromIntegration(
            $autoresponder,
            $integration
        );

        $lists = $autoresponderInstance->getLists();

        if (
            $autoresponderInstance->withOAuth() &&
            $autoresponderInstance->dirtyAccessToken()
        ) {
            $this->storeFreshAccessToken(
                $userId,
                $autoresponder,
                $autoresponderInstance->getFreshAccessToken()
            );
        }

        return $lists;
    }

    /**
     * Add a subscriber to the autoresponder list.
     *
     * @param  int  $userId
     * @param  string  $autoresponder
     * @param  string  $listId
     * @param  array  $subscriber
     * @return void
     */
    public function addSubscriberToList(
        $userId,
        $autoresponder,
        $listId,
        $subscriber
    ) {
        /** @var \App\Models\Integration|null */
        $integration = Integration::query()
            ->where('user_id', $userId)
            ->where('type', $autoresponder)
            ->first();

        if (is_null($integration)) {
            throw new AutoresponderServiceException(
                "The autoresponder doesn't exists.",
                400
            );
        }

        if ($this->isWebHook($autoresponder)) {
            (new Webhook($integration->api))->post(
                $subscriber['email'],
                $subscriber['firstName'],
                $subscriber['lastName'],
                $subscriber['street'],
                $subscriber['zipcode'],
                $subscriber['phone']
            );
        } else {
            $autoresponderInstance = $this->createAutoresponderFromIntegration(
                $autoresponder,
                $integration
            );

            $autoresponderInstance->addSubscriber(
                $listId,
                $subscriber['email'],
                $subscriber['firstName'],
                $subscriber['lastName']
            );
        }
    }

    /**
     * Creates a new Autoresponder from the integration data.
     *
     * @param  name  $autoresponder
     * @param  \App\Models\Integration  $integration
     * @return \App\Utils\Autoresponder\Driver|\App\Utils\Autoresponder\WithOAuthDriver
     */
    public function createAutoresponderFromIntegration(
        $autoresponder,
        $integration
    ) {
        $autoresponderInstance = Autoresponder::factory($autoresponder);

        if ($autoresponderInstance->withOAuth()) {
            $autoresponderInstance
                ->setAccessToken($integration->accessToken)
                ->setRefreshToken($integration->refreshToken)
                ->setExpiresIn($integration->accessTokenExpiresIn);
        } else {
            $autoresponderInstance->setApiKey($integration->api);

            if ($autoresponderInstance instanceof Mailchimp) {
                $autoresponderInstance->setDatacenter(
                    $integration->mailchimp_datacenter
                );
            }
        }

        return $autoresponderInstance;
    }

    /**
     * Store the fresh (renewed) access token.
     *
     * @param  int  $userId
     * @param  string  $autoresponder
     * @param  \App\Utils\Autoresponder\OAuthAccessToken  $accessToken
     * @return bool
     */
    public function storeFreshAccessToken($userId, $autoresponder, $accessToken)
    {
        $stored = DB::transaction(function () use (
            $userId,
            $autoresponder,
            $accessToken
        ) {
            /** @var \App\Models\Integration|null */
            $integration = Integration::query()
                ->select('id', 'oauth_data')
                ->where('user_id', $userId)
                ->where('type', $autoresponder)
                ->lockForUpdate()
                ->first();

            if (is_null($integration)) {
                return false;
            }

            $oauthData = [
                'access_token' => (string) $accessToken->getAccessToken(),
                'refresh_token' => (string) $accessToken->getRefreshToken(),
                'expires_in' => (int) $accessToken->getExpiresIn(),
            ];

            if (is_array($integration->oauth_data)) {
                $oauthData = array_merge($integration->oauth_data, $oauthData);
            } else {
                $oauthData['name'] = 'N/A';
            }

            return $integration->update([
                'oauth_data' => $oauthData,
            ]);
        });

        return $stored;
    }
}
