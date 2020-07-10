<?php
declare(strict_types=1);

namespace App\Option;

class AccessLog extends Response
{
    public function create()
    {
        $get = new \App\Option\Method\Get();
        $this->verbs['GET'] = $get->setParameters('api.request-access-log.parameters.collection')->
            setPaginationStatus(true)->
            setAuthenticationStatus($this->permissions['view'])->
            setDescription('route-descriptions.request_GET_access-log')->
            option();

        return $this;
    }
}
