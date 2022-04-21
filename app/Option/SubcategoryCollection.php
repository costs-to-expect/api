<?php
declare(strict_types=1);

namespace App\Option;

use Illuminate\Support\Facades\Config;

class SubcategoryCollection extends Response
{
    public function create()
    {
        $get = new \App\Method\GetRequest();
        $this->verbs['GET'] = $get->setSortableParameters(Config::get('api.subcategory.sortable'))->
            setSearchableParameters(Config::get('api.subcategory.searchable'))->
            setPaginationStatus(true, true)->
            setParameters(Config::get('api.subcategory.parameters'))->
            setDescription('route-descriptions.sub_category_GET_index')->
            setAuthenticationStatus($this->permissions['view'])->
            option();

        $post = new \App\Method\PostRequest();
        $this->verbs['POST'] = $post->setFields(Config::get('api.subcategory.fields-post'))->
            setDescription('route-descriptions.sub_category_POST')->
            setAuthenticationRequirement(true)->
            setAuthenticationStatus($this->permissions['manage'])->
            option();

        return $this;
    }
}
