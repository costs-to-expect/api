<?php
declare(strict_types=1);

namespace App\Option;

class SummaryResourceTypeItemCollection extends Response
{
    public function create()
    {
        $get = new \App\Option\Method\Get();
        $this->verbs['GET'] = $get->setSearchableParameters($this->interface->searchParametersConfig())->
            setParameters($this->interface->collectionParametersConfig())->
            setFilterableParameters($this->interface->filterParametersConfig())->
            setDescription('route-descriptions.summary-resource-type-item-GET-index')->
            setAuthenticationStatus($this->permissions['view'])->
            option();

        return $this;
    }
}
