<?php
declare(strict_types=1);

namespace App\Option;

class ItemCategoryCollection extends Response
{
    public function create()
    {
        $get = new \App\Option\Method\Get();
        $this->verbs['GET'] = $get->setAuthenticationStatus($this->permissions['view'])->
            setParameters('api.item-category.parameters.collection')->
            setDescription('route-descriptions.item_category_GET_index')->
            option();

        $post = new \App\Option\Method\Post();
        $this->verbs['POST'] = $post->setFields('api.item-category.fields')->
            setDynamicFields($this->allowed_values)->
            setAuthenticationRequirement(true)->
            setAuthenticationStatus($this->permissions['manage'])->
            setDescription('route-descriptions.item_category_POST')->
            option();

        return $this;
    }
}
