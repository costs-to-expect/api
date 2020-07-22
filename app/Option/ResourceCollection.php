<?php
declare(strict_types=1);

namespace App\Option;

class ResourceCollection extends Response
{
    public function create()
    {
        $get = new \App\Option\Method\Get();
        $this->verbs['GET'] = $get->setSortableParameters('api.resource.sortable')->
            setSearchableParameters('api.resource.searchable')->
            setPaginationStatus(true, true)->
            setParameters('api.resource.parameters.collection')->
            setAuthenticationStatus($this->permissions['view'])->
            setDescription('route-descriptions.resource_GET_index')->
            option();

        $post = new \App\Option\Method\Post();
        $this->verbs['POST'] = $post->setFields('api.resource.fields')->
            setDescription('route-descriptions.resource_POST')->
            setAuthenticationStatus($this->permissions['manage'])->
            setAuthenticationRequirement(true)->
            option();

        return $this;
    }
}
