<?php
declare(strict_types=1);

namespace App\Option;

use Illuminate\Support\Facades\Config;

class ResourceTypeCollection extends Response
{
    public function create()
    {
        $get = new \App\Method\GetRequest();
        $this->verbs['GET'] = $get->setSortableParameters(Config::get('api.resource-type.sortable'))->
            setSearchableParameters(Config::get('api.resource-type.searchable'))->
            setPaginationStatus(true, true)->
            setDescription('route-descriptions.resource_type_GET_index')->
            setAuthenticationStatus($this->permissions['view'])->
            option();

        $post = new \App\Method\PostRequest();
        $this->verbs['POST'] = $post->setFields(Config::get('api.resource-type.fields'))->
            setDynamicFields($this->allowed_fields)->
            setDescription('route-descriptions.resource_type_POST')->
            setAuthenticationStatus($this->permissions['manage'])->
            setAuthenticationRequirement(true)->
            option();

        return $this;
    }
}
