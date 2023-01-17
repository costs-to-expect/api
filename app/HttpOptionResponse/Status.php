<?php

declare(strict_types=1);

namespace App\HttpOptionResponse;

class Status extends Response
{
    public function create()
    {
        $get = new \App\HttpVerb\Get();
        $this->verbs['GET'] = $get->setAuthenticationStatus($this->permissions['view'])->
            setDescription('route-descriptions.api_GET_status')->
            option();

        return $this;
    }
}
