<?php

namespace App\Utils\Autoresponder;

use InvalidArgumentException;

class Autoresponder
{
    /**
     * Create an autoresponder instance.
     *
     * @param  string  $name The autoresponder name.
     * @param  array|null  $params The autoresponder parameters.
     * @return \App\Utils\Autoresponder\Driver|\App\Utils\Autoresponder\WithOAuthDriver|\App\Utils\Autoresponder\Mailchimp
     */
    public static function factory($name, $params = null)
    {
        $className = 'App\Utils\Autoresponder\\' . ucfirst(strtolower($name));

        if (!class_exists($className)) {
            throw new InvalidArgumentException(
                "The autoresponder '$name' is not supported."
            );
        }

        if (is_null(config("autoresponder.$name"))) {
            throw new InvalidArgumentException(
                "The autoresponder $name is not configured."
            );
        }

        return (new Autoresponder())->createDriver($className, $params);
    }

    /**
     * Create a new instance of the given autoresponder name.
     *
     * @param  string  $className
     * @param  array|null  $params
     * @return \App\Utils\Autoresponder\Driver|\App\Utils\Autoresponder\WithOAuthDriver|\App\Utils\Autoresponder\Mailchimp
     * @throws \InvalidArgumentException
     */
    protected function createDriver($className, $params = null)
    {
        $driver = new $className();

        if (is_null($params)) {
            return $driver;
        }

        if (!$driver->withOAuth()) {
            return $this->initDriver($driver, $params);
        }

        return $this->initWithOAuthDriver($driver, $params);
    }

    /**
     * Initialize parameters of the given driver.
     *
     * @param  \App\Utils\Autoresponder\Driver  $driver
     * @param  array  $params
     * @return \App\Utils\Autoresponder\Driver
     * @throws \InvalidArgumentException
     */
    protected function initDriver($driver, $params)
    {
        $this->throwIfParametersMissing($params, ['api_key']);
        $driver->setApiKey($params['api_key']);

        return $driver;
    }

    /**
     * Initialize parameters of the given OAuth 2.0 driver.
     *
     * @param  \App\Utils\Autoresponder\WithOAuthDriver  $driver
     * @param  array  $params
     * @return \App\Utils\Autoresponder\WithOAuthDriver
     * @throws \InvalidArgumentException
     */
    protected function initWithOAuthDriver($driver, $params)
    {
        $this->throwIfParametersMissing(
            $params,
            array_merge(['access_token', 'refresh_token', 'expires_in'])
        );
        $driver
            ->setAccessToken($params['access_token'])
            ->setRefreshToken($params['refresh_token'])
            ->setExpiresIn($params['expires_in']);

        if ($driver instanceof Mailchimp) {
            $this->throwIfParametersMissing($params, ['datacenter']);
            $driver->setDatacenter($params['datacenter']);
        }

        return $driver;
    }

    /**
     * Throw an exception if one or more of the given keys are missing
     * in the given parameters array.
     *
     * @param  array  $params
     * @param  array  $keys
     * @return void
     * @throws \InvalidArgumentException
     */
    protected function throwIfParametersMissing($params, $keys)
    {
        if (!is_array($params)) {
            throw new InvalidArgumentException(
                '`params` should be of type array.'
            );
        }

        if (!is_array($keys)) {
            throw new InvalidArgumentException(
                '`keys` should be of type array.'
            );
        }

        $missingKeys = [];
        foreach ($keys as $key) {
            if (!array_key_exists($key, $params)) {
                array_push($missingKeys, $key);
            }
        }

        if (count($missingKeys) > 0) {
            throw new InvalidArgumentException(
                sprintf(
                    'The following parameter%s `%s` %s missing.',
                    count($missingKeys) === 1 ? '' : 's',
                    implode('`, `', $missingKeys),
                    count($missingKeys) === 1 ? 'is' : 'are'
                )
            );
        }
    }
}
