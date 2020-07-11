<?php
declare(strict_types=1);

namespace App\Option;

class SummaryAccessLog extends Response
{
    public function create()
    {
        $get = new \App\Option\Method\Get();
        $this->verbs['GET'] = $get->setParameters('api.request-access-log.parameters.collection')->
            setDescription('route-descriptions.summary_GET_request_access-log')->
            setAuthenticationStatus($this->permissions['view'])->
            option();

        return $this;
    }
}
