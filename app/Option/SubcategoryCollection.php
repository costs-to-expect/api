<?php
declare(strict_types=1);

namespace App\Option;

use Illuminate\Support\Facades\Config;

class SubcategoryCollection extends Response
{
    public function create()
    {
        $get = new \App\Option\Method\GetRequest();
        $this->verbs['GET'] = $get->setSortableParameters(Config::get('api.subcategory.sortable'))->
            setSearchableParameters(Config::get('api.subcategory.searchable'))->
            setPaginationStatus(true, true)->
            setParameters(Config::get('api.subcategory.parameters.collection'))->
            setDescription('route-descriptions.sub_category_GET_index')->
            setAuthenticationStatus($this->permissions['view'])->
            option();

        $post = new \App\Option\Method\PostRequest();
        $this->verbs['POST'] = $post->setFields(Config::get('api.subcategory.fields'))->
            setDescription('route-descriptions.sub_category_POST')->
            setAuthenticationRequirement(true)->
            setAuthenticationStatus($this->permissions['manage'])->
            option();

        return $this;
    }
}
