<?php

declare(strict_types=1);

namespace App\HttpOptionResponse;

use Illuminate\Support\Facades\Config;

class CategoryCollection extends Response
{
    public function create()
    {
        $get = new \App\HttpVerb\Get();
        $this->verbs['GET'] = $get->setPaginationStatus(true, true)->
            setParameters(Config::get('api.category.parameters'))->
            setSearchableParameters(Config::get('api.category.searchable'))->
            setSortableParameters(Config::get('api.category.sortable'))->
            setAuthenticationStatus($this->permissions['view'])->
            setDescription('route-descriptions.category_GET_index')->
            option();

        $post = new \App\HttpVerb\Post();
        $this->verbs['POST'] = $post->setFields(Config::get('api.category.fields-post'))->
            setAuthenticationRequirement(true)->
            setAuthenticationStatus($this->permissions['manage'])->
            setDescription('route-descriptions.category_POST')->
            option();

        return $this;
    }
}
