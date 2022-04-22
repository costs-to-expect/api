<?php
declare(strict_types=1);

namespace App\Option;

class Cache extends Response
{
    public function create()
    {
        $get = new \App\HttpVerb\Get();
        $this->verbs['GET'] = $get->setAuthenticationStatus($this->permissions['view'])->
            setAuthenticationRequirement(true)->
            setDescription('route-descriptions.request_GET_cache')->
            option();

        $this->verbs['DELETE'] = $get->setAuthenticationStatus($this->permissions['manage'])->
            setAuthenticationRequirement(true)->
            setDescription('route-descriptions.request_DELETE_cache')->
            option();

        return $this;
    }
}
