<?php
declare(strict_types=1);

namespace App\Option;

class SummaryItemCollection extends Response
{
    public function create()
    {
        $get = new \App\Option\Method\Get();
        $this->verbs['GET'] = $get->setParameters($this->interface->collectionParametersConfig())->
            setSearchableParameters($this->interface->searchParametersConfig())->
            setFilterableParameters($this->interface->filterParametersConfig())->
            setDescription('route-descriptions.summary_GET_resource-type_resource_items')->
            setAuthenticationStatus($this->permissions['view'])->
            option();

        return $this;
    }
}
