<?php
declare(strict_types=1);

namespace App\Option;

class ResourceTypeItemCollection extends Response
{
    public function create()
    {
        $get = new \App\Option\Method\Get();
        $this->verbs['GET'] = $get->setSortableParameters($this->interface->sortParametersConfig())->
            setSearchableParameters($this->interface->searchParametersConfig())->
            setFilterableParameters($this->interface->filterParametersConfig())->
            setPaginationStatus(true)->
            setParameters($this->interface->collectionParametersConfig())->
            setDynamicParameters($this->allowed_values)->
            setDescription('route-descriptions.resource_type_item_GET_index')->
            setAuthenticationStatus($this->permissions['view'])->
            option();

        return $this;
    }
}
