<?php
declare(strict_types=1);

namespace App\Option;

class ClearCache extends Response
{
    public function create()
    {
        $get = new \App\Option\Method\Get();
        $this->verbs['GET'] = $get->setAuthenticationStatus($this->permissions['manage'])->
            setAuthenticationRequirement(true)->
            setDescription('route-descriptions.request_GET_clear-cache')->
            option();

        return $this;
    }
}
