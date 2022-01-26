<?php
declare(strict_types=1);

namespace App\Option\Auth;

use App\Option\Response;
use Illuminate\Support\Facades\Config;

class Register extends Response
{
    public function create()
    {
        $post = new \App\Method\PostRequest();
        $this->verbs['POST'] = $post->setFields(Config::get('api.auth.register.fields'))
            ->setAuthenticationRequirement()
            ->setDescription('route-descriptions.auth_register_POST')
            ->option();

        return $this;
    }
}
