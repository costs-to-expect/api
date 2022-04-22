<?php
declare(strict_types=1);

namespace App\Option\Auth;

use App\Option\Response;
use Illuminate\Support\Facades\Config;

class Login extends Response
{
    public function create()
    {
        $post = new \App\HttpVerb\Post();
        $this->verbs['POST'] = $post->setFields(Config::get('api.auth.login.fields-post'))
            ->setAuthenticationRequirement()
            ->setDescription('route-descriptions.auth_login_POST')
            ->option();

        return $this;
    }
}
