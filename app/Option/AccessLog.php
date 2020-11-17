<?php
declare(strict_types=1);

namespace App\Option;

use Illuminate\Support\Facades\Config;

class AccessLog extends Response
{
    public function create()
    {
        $get = new \App\Method\GetRequest();
        $this->verbs['GET'] = $get->setParameters(Config::get('api.request-access-log.parameters.collection'))->
            setPaginationStatus(true)->
            setAuthenticationStatus($this->permissions['view'])->
            setDescription('route-descriptions.request_GET_access-log')->
            option();

        return $this;
    }
}
