<?php
declare(strict_types=1);

namespace App\Option;

class ResourceTypeItemCollection extends Response
{
    public function create()
    {
        $get = new \App\Option\Method\GetRequest();
        $this->verbs['GET'] = $get->setSortableParameters($this->entity->resourceTypeSortParameters())->
            setSearchableParameters($this->entity->resourceTypeSearchParameters())->
            setFilterableParameters($this->entity->resourceTypeFilterParameters())->
            setPaginationStatus(true)->
            setParameters($this->entity->resourceTypeRequestParameters())->
            setDynamicParameters($this->allowed_values)->
            setDescription('route-descriptions.resource_type_item_GET_index')->
            setAuthenticationStatus($this->permissions['view'])->
            option();

        return $this;
    }
}
