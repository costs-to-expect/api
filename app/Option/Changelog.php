<?php
declare(strict_types=1);

namespace App\Option;

class Changelog extends Response
{
    public function create()
    {
        $get = new \App\HttpVerb\GetReponse();
        $this->verbs['GET'] = $get->setAuthenticationStatus($this->permissions['view'])->
            setDescription('route-descriptions.api_GET_index')->
            option();

        return $this;
    }
}
