<?php
declare(strict_types=1);

namespace App\Option;

class CategoryCollection extends Response
{
    public function create()
    {
        $get = new \App\Option\Method\Get();
        $this->verbs['GET'] = $get->setPaginationStatus(true, true)->
            setParameters('api.category.parameters.collection')->
            setSearchableParameters('api.category.searchable')->
            setSortableParameters('api.category.sortable')->
            setAuthenticationStatus($this->permissions['view'])->
            setDescription('route-descriptions.category_GET_index')->
            option();

        $post = new \App\Option\Method\Post();
        $this->verbs['POST'] = $post->setFields('api.category.fields')->
            setAuthenticationRequirement(true)->
            setAuthenticationStatus($this->permissions['manage'])->
            setDescription('route-descriptions.category_POST')->
            option();

        return $this;
    }
}
