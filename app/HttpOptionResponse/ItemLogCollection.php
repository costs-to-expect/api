<?php

declare(strict_types=1);

namespace App\HttpOptionResponse;

use Illuminate\Support\Facades\Config;

class ItemLogCollection extends Response
{
    public function create()
    {
        $this->verbs['GET'] = (new \App\HttpVerb\Get())
            ->setAuthenticationStatus($this->permissions['view'])
            ->setDescription('route-descriptions.item_log_GET_index')
            ->option();

        $this->verbs['POST'] = (new \App\HttpVerb\Post())
            ->setFields(Config::get('api.item-log.fields-post'))
            ->setDescription('route-descriptions.item_log_POST')
            ->setAuthenticationStatus($this->permissions['manage'])
            ->setAuthenticationRequirement(true)
            ->option();

        return $this;
    }
}
