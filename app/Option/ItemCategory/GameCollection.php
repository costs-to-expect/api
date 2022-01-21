<?php
declare(strict_types=1);

namespace App\Option\ItemCategory;

use App\Option\Response;
use Illuminate\Support\Facades\Config;

class GameCollection extends Response
{
    public function create()
    {
        $get = new \App\Method\GetRequest();
        $this->verbs['GET'] = $get->setAuthenticationStatus($this->permissions['view'])->
            setParameters(Config::get('api.item-category.parameters.collection'))->
            setDescription('route-descriptions.item_category_GET_index')->
            option();

        $post = new \App\Method\PostRequest();
        $this->verbs['POST'] = $post->setFields(Config::get('api.item-category.fields'))->
            setDynamicFields($this->allowed_fields)->
            setAuthenticationRequirement(true)->
            setAuthenticationStatus($this->permissions['manage'])->
            setDescription('route-descriptions.item_category_POST_game')->
            option();

        return $this;
    }
}
