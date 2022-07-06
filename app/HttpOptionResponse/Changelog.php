<?php

declare(strict_types=1);

namespace App\HttpOptionResponse;

class Changelog extends Response
{
    public function create()
    {
        $get = new \App\HttpVerb\Get();
        $this->verbs['GET'] = $get->setAuthenticationStatus($this->permissions['view'])->
            setDescription('route-descriptions.api_GET_index')->
            option();

        return $this;
    }
}
