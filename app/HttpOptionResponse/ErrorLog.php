<?php
declare(strict_types=1);

namespace App\HttpOptionResponse;

use Illuminate\Support\Facades\Config;

class ErrorLog extends Response
{
    public function create()
    {
        $get = new \App\HttpVerb\Get();
        $this->verbs['GET'] = $get->setDescription('route-descriptions.request_GET_error_log')->
            setAuthenticationStatus($this->permissions['view'])->
            option();

        $post = new \App\HttpVerb\Post();
        $this->verbs['POST'] = $post->setFields(Config::get('api.request-error-log.fields-post'))->
            setDescription('route-descriptions.request_POST')->
            setAuthenticationStatus($this->permissions['view'])->
            option();

        return $this;
    }
}
