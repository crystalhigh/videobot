<?php

namespace App\Utils\Autoresponder;

trait Formattable
{
    /**
     * Make a nice looking full name.
     *
     * @param  string  $firstName
     * @param  string  $lastName
     * @return string
     */
    protected function makeFullname($firstName = '', $lastName = '')
    {
        return trim(
            $this->formatName($firstName) . ' ' . $this->formatName($lastName)
        );
    }

    /**
     * Format the given name.
     *
     * @param  string $name
     * @return string
     */
    protected function formatName($name)
    {
        return ucfirst(strtolower($name));
    }
}
