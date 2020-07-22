<?php
declare(strict_types=1);

namespace App\Option;

class SummaryResourceCollection extends Response
{
    public function create()
    {
        $get = new \App\Option\Method\Get();
        $this->verbs['GET'] = $get->setParameters('api.resource.summary-parameters')->
            setDescription('route-descriptions.summary-resource-GET-index')->
            setAuthenticationStatus($this->permissions['view'])->
            setSearchableParameters('api.resource.summary-searchable')->
            option();

        return $this;
    }
}
