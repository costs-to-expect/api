<?php
declare(strict_types=1);

namespace App\Option;

class SummaryResourceTypeCollection extends Response
{
    public function create()
    {
        $get = new \App\Option\Method\Get();
        $this->verbs['GET'] = $get->setParameters('api.resource-type.summary-parameters')->
            setDescription('route-descriptions.summary-resource-type-GET-index')->
            setAuthenticationStatus($this->permissions['view'])->
            setSearchableParameters('api.resource-type.summary-searchable')->
            option();

        return $this;
    }
}
