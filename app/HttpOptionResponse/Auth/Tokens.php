<?php
declare(strict_types=1);

namespace App\HttpOptionResponse\Auth;

use App\HttpOptionResponse\Response;

class Tokens extends Response
{
    public function create()
    {
        $get = new \App\HttpVerb\Get();
        $this->verbs['GET'] = $get->setSortableParameters([])->
            setSearchableParameters([])->
            setDescription('route-descriptions.auth_user_tokens_GET')->
            setAuthenticationRequirement(true)->
            setAuthenticationStatus($this->permissions['view'])->
            option();

        return $this;
    }
}
