<?php
declare(strict_types=1);

namespace App\Option\Auth;

use App\Option\Response;

class Token extends Response
{
    public function create()
    {
        $get = new \App\HttpVerb\Get();
        $this->verbs['GET'] = $get->setDescription('route-descriptions.auth_user_token_GET')->
            setAuthenticationRequirement(true)->
            setAuthenticationStatus($this->permissions['view'])->
            option();

        $delete = new \App\HttpVerb\Delete();
        $this->verbs['DELETE'] = $delete->setDescription('route-descriptions.auth_user_token_DELETE')->
            setAuthenticationRequirement(true)->
            setAuthenticationStatus($this->permissions['manage'])->
            option();

        return $this;
    }
}
