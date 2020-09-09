<?php
declare(strict_types=1);

namespace App\Option;

class Root extends Response
{
    public function create()
    {
        $get = new \App\Option\Method\GetRequest();
        $this->verbs['GET'] = $get->setAuthenticationStatus($this->permissions['view'])->
            setDescription('route-descriptions.api_GET_changelog')->
            option();

        return $this;
    }
}
