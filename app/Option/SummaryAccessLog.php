<?php
declare(strict_types=1);

namespace App\Option;

use Illuminate\Support\Facades\Config;

class SummaryAccessLog extends Response
{
    public function create()
    {
        $get = new \App\Method\GetRequest();
        $this->verbs['GET'] = $get->setParameters(Config::get('api.request-access-log.parameters.collection'))->
            setDescription('route-descriptions.summary_GET_request_access-log')->
            setAuthenticationStatus($this->permissions['view'])->
            option();

        return $this;
    }
}
