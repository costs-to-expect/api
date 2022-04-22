<?php
declare(strict_types=1);

namespace App\Option\Auth;

use App\Option\Response;

class User extends Response
{
    public function create()
    {
        $get = new \App\HttpVerb\GetReponse();
        $this->verbs['GET'] = $get->setSortableParameters([])->
            setSearchableParameters([])->
            setDescription('route-descriptions.auth_user_GET')->
            setAuthenticationRequirement(true)->
            setAuthenticationStatus($this->permissions['view'])->
            option();

        return $this;
    }
}
