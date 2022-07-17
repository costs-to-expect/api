<?php

declare(strict_types=1);

namespace App\HttpOptionResponse;

use Illuminate\Support\Facades\Config;

class ItemDataItem extends Response
{
    public function create()
    {
        $this->verbs['GET'] = (new \App\HttpVerb\Get())
            ->setAuthenticationStatus($this->permissions['view'])
            ->setDescription('route-descriptions.item_data_GET_show')
            ->option();

        $this->verbs['DELETE'] = (new \App\HttpVerb\Delete())
            ->setDescription('route-descriptions.item_data_DELETE')
            ->setAuthenticationRequirement(true)
            ->setAuthenticationStatus($this->permissions['manage'])
            ->option();

        $this->verbs['PATCH'] = (new \App\HttpVerb\Patch())
            ->setFields(Config::get('api.item-data.fields-patch'))
            ->setDescription('route-descriptions.item_data_PATCH')
            ->setAuthenticationRequirement(true)
            ->setAuthenticationStatus($this->permissions['manage'])
            ->option();

        return $this;
    }
}
