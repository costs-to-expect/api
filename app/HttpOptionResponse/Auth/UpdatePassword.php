<?php

declare(strict_types=1);

namespace App\HttpOptionResponse\Auth;

use App\HttpOptionResponse\Response;
use Illuminate\Support\Facades\Config;

class UpdatePassword extends Response
{
    public function create()
    {
        $post = new \App\HttpVerb\Post();
        $this->verbs['POST'] = $post->setFields(Config::get('api.auth.update-password.fields-post'))
            ->setAuthenticationRequirement(true)
            ->setAuthenticationStatus($this->permissions['manage'])
            ->setDescription('route-descriptions.auth_update_password_POST')
            ->option();

        return $this;
    }
}
