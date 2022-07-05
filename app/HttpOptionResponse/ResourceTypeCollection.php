<?php
declare(strict_types=1);

namespace App\HttpOptionResponse;

use Illuminate\Support\Facades\Config;

class ResourceTypeCollection extends Response
{
    public function create()
    {
        $get = new \App\HttpVerb\Get();
        $this->verbs['GET'] = $get->setSortableParameters(Config::get('api.resource-type.sortable'))
            ->setSearchableParameters(Config::get('api.resource-type.searchable'))
            ->setParameters(Config::get('api.resource-type.parameters'))
            ->setAllowedValuesForParameters($this->allowed_values_for_parameters)
            ->setPaginationStatus(true, true)
            ->setDescription('route-descriptions.resource_type_GET_index')
            ->setAuthenticationStatus($this->permissions['view'])
            ->option();

        $post = new \App\HttpVerb\Post();
        $this->verbs['POST'] = $post->setFields(Config::get('api.resource-type.fields-post'))
            ->setAllowedValuesForFields($this->allowed_values_for_fields)
            ->setDescription('route-descriptions.resource_type_POST')
            ->setAuthenticationStatus($this->permissions['manage'])
            ->setAuthenticationRequirement(true)
            ->option();

        return $this;
    }
}
