<?php

return [
    'aweber' => [
        'endpoint' => env('AWEBER_API_ENDPOINT', null),
        'oauth_endpoint' => env('AWEBER_OAUTH_ENDPOINT', null),
        'scopes' => env('AWEBER_SCOPES', null),
        'client_id' => env('AWEBER_CLIENT_ID', null),
        'client_secret' => env('AWEBER_CLIENT_SECRET', null),
        'redirect_uri' => env('AWEBER_REDIRECT_URI', null),
    ],

    'mailchimp' => [
        'endpoint' => env('MAILCHIMP_API_ENDPOINT', null),
    ],

    'getresponse' => [
        'endpoint' => env('GETRESPONSE_API_ENDPOINT', null),
    ],

    'sendinblue' => [
        'endpoint' => env('SENDINBLUE_API_ENDPOINT', null),
    ],
];
