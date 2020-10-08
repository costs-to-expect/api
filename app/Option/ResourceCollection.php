<?php
declare(strict_types=1);

namespace App\Option;

use Illuminate\Support\Facades\Config;

class ResourceCollection extends Response
{
    public function create()
    {
        $get = new \App\Option\Method\GetRequest();
        $this->verbs['GET'] = $get
            ->setSortableParameters(Config::get('api.resource.sortable'))
            ->setSearchableParameters(Config::get('api.resource.searchable'))
            ->setPaginationStatus(true, true)
            ->setParameters(Config::get('api.resource.parameters.collection'))
            ->setAuthenticationStatus($this->permissions['view'])
            ->setDescription('route-descriptions.resource_GET_index')
            ->option();

        $post = new \App\Option\Method\PostRequest();
        $this->verbs['POST'] = $post
            ->setFields(Config::get('api.resource.fields'))
            ->setDynamicFields($this->allowed_values)
            ->setDescription('route-descriptions.resource_POST')
            ->setAuthenticationStatus($this->permissions['manage'])
            ->setAuthenticationRequirement(true)
            ->option();

        return $this;
    }
}
