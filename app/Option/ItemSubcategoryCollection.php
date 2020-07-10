<?php
declare(strict_types=1);

namespace App\Option;

class ItemSubcategoryCollection extends Response
{
    public function create()
    {
        $get = new \App\Option\Method\Get();
        $this->verbs['GET'] = $get->setParameters('api.item-subcategory.parameters.collection')->
            setAuthenticationStatus($this->permissions['view'])->
            setDescription('route-descriptions.item_sub_category_GET_index')->
            option();

        $post = new \App\Option\Method\Post();
        $this->verbs['POST'] = $post->setFields('api.item-subcategory.fields')->
            setDynamicFields($this->allowed_values)->
            setDescription('route-descriptions.item_sub_category_POST')->
            setAuthenticationStatus($this->permissions['manage'])->
            setAuthenticationRequirement(true)->
            option();

        return $this;
    }
}
