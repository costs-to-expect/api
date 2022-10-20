<?php

declare(strict_types=1);

namespace App\HttpOptionResponse\Auth;

use App\HttpOptionResponse\Response;
use Illuminate\Support\Facades\Config;

class RequestResourceTypeDelete extends Response
{
    public function create()
    {
        $post = new \App\HttpVerb\Post();
        $this->verbs['POST'] = $post->setFields(Config::get('api.auth.request-resource-type-delete.fields-post'))
            ->setAuthenticationRequirement(true)
            ->setAuthenticationStatus($this->permissions['manage'])
            ->setDescription('route-descriptions.auth_request_resource_type_delete_POST')
            ->option();

        return $this;
    }
}
