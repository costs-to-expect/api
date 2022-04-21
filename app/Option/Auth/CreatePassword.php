<?php
declare(strict_types=1);

namespace App\Option\Auth;

use App\Option\Response;
use Illuminate\Support\Facades\Config;

class CreatePassword extends Response
{
    public function create()
    {
        $post = new \App\Method\PostRequest();
        $this->verbs['POST'] = $post->setFields(Config::get('api.auth.create-password.fields-post'))
            ->setParameters(Config::get('api.auth.create-password.parameters'))
            ->setAuthenticationRequirement()
            ->setDescription('route-descriptions.auth_create_password_POST')
            ->option();

        return $this;
    }
}
