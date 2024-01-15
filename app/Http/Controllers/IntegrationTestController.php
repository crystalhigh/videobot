<?php

namespace App\Http\Controllers;

use App\Utils\Autoresponder\Autoresponder;
use App\Utils\Autoresponder\Mailchimp;
use Illuminate\Http\Client\RequestException;
use Throwable;

class IntegrationTestController extends Controller
{
    /**
     * Test an autoresponder.
     *
     * @param  string  $autoresponder The autoresponder's name.
     * @return \Illuminate\Http\Response
     * @throws \InvalidArgumentException
     * @throws \Throwable
     */
    public function test($autoresponder)
    {
        $response = null;
        $autoresponder = Autoresponder::factory($autoresponder);

        if ($autoresponder->withOAuth()) {
            $autoresponder
                ->setAccessToken(request('access_token'))
                ->setRefreshToken(request('refresh_token'))
                ->setExpiresIn(request('expires_in'));

            if ($autoresponder instanceof Mailchimp) {
                $autoresponder->setDatacenter(request('datacenter'));
            }
        } else {
            $autoresponder->setApiKey(request('api_key'));
        }

        try {
            $response = $autoresponder->testApi();
        } catch (Throwable $e) {
            if ($e instanceof RequestException) {
                dump($e->response->body());

                return response(
                    'please see the response body for more details!'
                );
            }

            throw $e;
        }

        return response($response);
    }
}
