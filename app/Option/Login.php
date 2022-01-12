<?php
declare(strict_types=1);

namespace App\Option;

use Illuminate\Support\Facades\Config;

class Login extends Response
{
    public function create()
    {
        $post = new \App\Method\PostRequest();
        $this->verbs['POST'] = $post->setFields(Config::get('api.auth.login.fields'))
            ->setAuthenticationRequirement()
            ->setDescription('route-descriptions.auth_login_POST')
            ->option();

        return $this;
    }
}
