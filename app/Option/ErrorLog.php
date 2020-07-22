<?php
declare(strict_types=1);

namespace App\Option;

class ErrorLog extends Response
{
    public function create()
    {
        $get = new \App\Option\Method\Get();
        $this->verbs['GET'] = $get->setDescription('route-descriptions.request_GET_error-log')->
            setAuthenticationStatus($this->permissions['view'])->
            option();

        $post = new \App\Option\Method\Post();
        $this->verbs['POST'] = $post->setFields('api.request-error-log.fields')->
            setDescription('route-descriptions.request_POST')->
            setAuthenticationStatus($this->permissions['view'])->
            option();

        return $this;
    }
}
