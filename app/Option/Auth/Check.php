<?php
declare(strict_types=1);

namespace App\Option\Auth;

use App\Option\Response;

class Check extends Response
{
    public function create()
    {
        $get = new \App\Method\GetRequest();
        $this->verbs['GET'] = $get->setSortableParameters([])->
            setSearchableParameters([])->
            setDescription('route-descriptions.auth_check_GET')->
            setAuthenticationRequirement(false)->
            option();

        return $this;
    }
}
