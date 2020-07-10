<?php
declare(strict_types=1);

namespace App\Option;

class ItemCollection extends Response
{
    public function create()
    {
        $get = new \App\Option\Method\Get();
        $this->verbs['GET'] = $get->setSortableParameters($item_interface->sortParametersConfig())->
            setSearchableParameters($item_interface->searchParametersConfig())->
            setFilterableParameters($item_interface->filterParametersConfig())->
            setParameters($item_interface->collectionParametersConfig())->
            setDynamicParameters($this->allowed_values)->
            setPaginationStatus(true)->
            setAuthenticationStatus($this->permissions['view'])->
            setDescription('route-descriptions.item_GET_index')->
            option();

        $post = new \App\Option\Method\Post();
        $this->verbs['POST'] = $post->setFields($item_interface->fieldsConfig())->
            setDescription( 'route-descriptions.item_POST')->
            setAuthenticationRequirement(true)->
            setAuthenticationStatus($this->permissions['manage'])->
            option();

        return $this;
    }
}
