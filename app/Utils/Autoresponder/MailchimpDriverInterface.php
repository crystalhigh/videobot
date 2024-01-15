<?php

namespace App\Utils\Autoresponder;

interface MailchimpDriverInterface extends DriverInterface
{
    /**
     * Set the datacenter.
     *
     * @param  string  $datacenter
     * @return void
     */
    public function setDatacenter($datacenter);
}
