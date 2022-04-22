<?php
declare(strict_types=1);

namespace App\HttpOptionResponse\Auth;

use App\HttpOptionResponse\Response;
use Illuminate\Support\Facades\Config;

class Register extends Response
{
    public function create()
    {
        $post = new \App\HttpVerb\Post();
        $this->verbs['POST'] = $post->setFields(Config::get('api.auth.register.fields-post'))
            ->setAuthenticationRequirement()
            ->setDescription('route-descriptions.auth_register_POST')
            ->option();

        return $this;
    }
}
