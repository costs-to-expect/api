<?php
declare(strict_types=1);

namespace App\Option;

use Illuminate\Support\Facades\Config;

class ItemSubcategoryCollection extends Response
{
    public function create()
    {
        $get = new \App\Method\GetRequest();
        $this->verbs['GET'] = $get->setParameters(Config::get('api.item-subcategory.parameters'))->
            setAuthenticationStatus($this->permissions['view'])->
            setDescription('route-descriptions.item_sub_category_GET_index')->
            option();

        $post = new \App\Method\PostRequest();
        $this->verbs['POST'] = $post->setFields(Config::get('api.item-subcategory.fields-post'))->
            setDynamicFields($this->allowed_fields)->
            setDescription('route-descriptions.item_sub_category_POST_' . $this->entity->type())->
            setAuthenticationStatus($this->permissions['manage'])->
            setAuthenticationRequirement(true)->
            option();

        return $this;
    }
}
