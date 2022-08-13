<?php

declare(strict_types=1);

namespace App\HttpOptionResponse;

use Illuminate\Support\Facades\Config;

class ItemLogItem extends Response
{
    public function create()
    {
        $this->verbs['GET'] = (new \App\HttpVerb\Get())
            ->setAuthenticationStatus($this->permissions['view'])
            ->setDescription('route-descriptions.item_log_GET_show')
            ->option();

        return $this;
    }
}
