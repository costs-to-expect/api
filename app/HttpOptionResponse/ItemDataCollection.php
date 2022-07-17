<?php

declare(strict_types=1);

namespace App\HttpOptionResponse;

use Illuminate\Support\Facades\Config;

class ItemDataCollection extends Response
{
    public function create()
    {
        $this->verbs['GET'] = (new \App\HttpVerb\Get())
            ->setAuthenticationStatus($this->permissions['view'])
            ->setDescription('route-descriptions.item_data_GET_index')
            ->option();

        $this->verbs['POST'] = (new \App\HttpVerb\Post())
            ->setFields(Config::get('api.item-data.fields-post'))
            ->setDescription('route-descriptions.resource_POST')
            ->setAuthenticationStatus($this->permissions['manage'])
            ->setAuthenticationRequirement(true)
            ->option();

        return $this;
    }
}
