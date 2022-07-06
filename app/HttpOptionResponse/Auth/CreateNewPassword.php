<?php

declare(strict_types=1);

namespace App\HttpOptionResponse\Auth;

use App\HttpOptionResponse\Response;
use Illuminate\Support\Facades\Config;

class CreateNewPassword extends Response
{
    public function create()
    {
        $post = new \App\HttpVerb\Post();
        $this->verbs['POST'] = $post->setFields(Config::get('api.auth.create-password.fields-post'))
            ->setAuthenticationRequirement()
            ->setDescription('route-descriptions.auth_create_new_password_POST')
            ->option();

        return $this;
    }
}
