<?php

namespace PHPSTORM_META {
    override(
        \App\Utils\Autoresponder\Autoresponder::init(),
        map([
            'getresponse' => \App\Utils\Autoresponder\Getresponse::class,
            'sendinblue' => \App\Utils\Autoresponder\Sendinblue::class,
            'aweber' => \App\Utils\Autoresponder\Aweber::class,
            'mailchimp' => \App\Utils\Autoresponder\Mailchimp::class,
        ])
    );
}
