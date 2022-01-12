<?php
declare(strict_types=1);

namespace App\Option;

use Illuminate\Support\Facades\Config;

class CreateNewPassword extends Response
{
    public function create()
    {
        $post = new \App\Method\PostRequest();
        $this->verbs['POST'] = $post->setFields(Config::get('api.auth.create-new-password.fields'))
            ->setAuthenticationRequirement()
            ->setDescription('route-descriptions.auth_create_new_password_POST')
            ->option();

        return $this;
    }
}
