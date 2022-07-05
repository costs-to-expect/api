<?php

declare(strict_types=1);

namespace App\HttpOptionResponse;

class CurrencyItem extends Response
{
    public function create()
    {
        $get = new \App\HttpVerb\Get();
        $this->verbs['GET'] = $get->setDescription('route-descriptions.currency_GET_show')->
            setAuthenticationStatus($this->permissions['view'])->
            option();

        return $this;
    }
}
