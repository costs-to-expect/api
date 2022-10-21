<?php

declare(strict_types=1);

namespace App\HttpOptionResponse\Auth;

use App\HttpOptionResponse\Response;

class RequestResourceDelete extends Response
{
    public function create()
    {
        $post = new \App\HttpVerb\Post();
        $this->verbs['POST'] = $post->setAuthenticationRequirement(true)
            ->setAuthenticationStatus($this->permissions['manage'])
            ->setDescription('route-descriptions.auth_request_resource_delete_POST')
            ->option();

        return $this;
    }
}
