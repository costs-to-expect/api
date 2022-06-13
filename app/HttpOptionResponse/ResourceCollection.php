<?php
declare(strict_types=1);

namespace App\HttpOptionResponse;

use Illuminate\Support\Facades\Config;

class ResourceCollection extends Response
{
    public function create()
    {
        $get = new \App\HttpVerb\Get();
        $this->verbs['GET'] = $get
            ->setSortableParameters(Config::get('api.resource.sortable'))
            ->setSearchableParameters(Config::get('api.resource.searchable'))
            ->setPaginationStatus(true, true)
            ->setParameters(Config::get('api.resource.parameters'))
            ->setAuthenticationStatus($this->permissions['view'])
            ->setDescription('route-descriptions.resource_GET_index')
            ->option();

        $post = new \App\HttpVerb\Post();
        $this->verbs['POST'] = $post
            ->setFields(Config::get('api.resource.fields-post'))
            ->setDynamicFields($this->allowed_fields)
            ->setDescription('route-descriptions.resource_POST')
            ->setAuthenticationStatus($this->permissions['manage'])
            ->setAuthenticationRequirement(true)
            ->option();

        return $this;
    }
}
