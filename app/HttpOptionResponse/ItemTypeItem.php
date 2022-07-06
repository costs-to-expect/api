<?php

declare(strict_types=1);

namespace App\HttpOptionResponse;

class ItemTypeItem extends Response
{
    public function create()
    {
        $get = new \App\HttpVerb\Get();
        $this->verbs['GET'] = $get->setDescription('route-descriptions.item_type_GET_show')->
            setAuthenticationStatus($this->permissions['view'])->
            option();

        return $this;
    }
}
