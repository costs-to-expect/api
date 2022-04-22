<?php
declare(strict_types=1);

namespace App\Option\Auth;

use App\Option\Response;
use Illuminate\Support\Facades\Config;

class ForgotPassword extends Response
{
    public function create()
    {
        $post = new \App\HttpVerb\Post();
        $this->verbs['POST'] = $post->setFields(Config::get('api.auth.forgot-password.fields-post'))
            ->setAuthenticationRequirement()
            ->setDescription('route-descriptions.auth_forgot_password_POST')
            ->option();

        return $this;
    }
}
