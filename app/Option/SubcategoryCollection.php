<?php
declare(strict_types=1);

namespace App\Option;

class SubcategoryCollection extends Response
{
    public function create()
    {
        $get = new \App\Option\Method\Get();
        $this->verbs['GET'] = $get->setSortableParameters('api.subcategory.sortable')->
            setSearchableParameters('api.subcategory.searchable')->
            setPaginationStatus(true, true)->
            setParameters('api.subcategory.parameters.collection')->
            setDescription('route-descriptions.sub_category_GET_index')->
            setAuthenticationStatus($this->permissions['view'])->
            option();

        $post = new \App\Option\Method\Post();
        $this->verbs['POST'] = $post->setFields('api.subcategory.fields')->
            setDescription('route-descriptions.sub_category_POST')->
            setAuthenticationRequirement(true)->
            setAuthenticationStatus($this->permissions['manage'])->
            option();

        return $this;
    }
}
