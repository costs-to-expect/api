<?php
declare(strict_types=1);

namespace App\Option;

class ResourceTypeCollection extends Response
{
    public function create()
    {
        $get = new \App\Option\Method\Get();
        $this->verbs['GET'] = $get->setSortableParameters('api.resource-type.sortable')->
            setSearchableParameters('api.resource-type.searchable')->
            setPaginationStatus(true, true)->
            setDescription('route-descriptions.resource_type_GET_index')->
            setAuthenticationStatus($this->permissions['view'])->
            option();

        $post = new \App\Option\Method\Post();
        $this->verbs['POST'] = $post->setFields('api.resource-type.fields')->
            setDynamicFields($this->allowed_values)->
            setDescription('route-descriptions.resource_type_POST')->
            setAuthenticationStatus($this->permissions['manage'])->
            setAuthenticationRequirement(true)->
            option();

        return $this;
    }
}
