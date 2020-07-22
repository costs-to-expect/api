<?php
declare(strict_types=1);

namespace App\Option;

class ItemCollection extends Response
{
    public function create()
    {
        $get = new \App\Option\Method\Get();
        $this->verbs['GET'] = $get->setSortableParameters($this->interface->sortParametersConfig())->
            setSearchableParameters($this->interface->searchParametersConfig())->
            setFilterableParameters($this->interface->filterParametersConfig())->
            setParameters($this->interface->collectionParametersConfig())->
            setDynamicParameters($this->allowed_values)->
            setPaginationStatus(true)->
            setAuthenticationStatus($this->permissions['view'])->
            setDescription('route-descriptions.item_GET_index')->
            option();

        $post = new \App\Option\Method\Post();
        $this->verbs['POST'] = $post->setFields($this->interface->fieldsConfig())->
            setDescription( 'route-descriptions.item_POST')->
            setAuthenticationRequirement(true)->
            setAuthenticationStatus($this->permissions['manage'])->
            option();

        return $this;
    }
}
