<?php
declare(strict_types=1);

namespace App\HttpOptionResponse\ResourceTypeItem;

use App\HttpOptionResponse\Response;
use Illuminate\Support\Facades\Config as LaravelConfig;

class SimpleItemCollection extends Response
{
    public function create()
    {
        $base_path = 'api.resource-type-item-type-simple-item';

        $get = new \App\HttpVerb\Get();
        $this->verbs['GET'] = $get->setSortableParameters(LaravelConfig::get($base_path . '.sortable', []))->
            setSearchableParameters(LaravelConfig::get($base_path . '.searchable', []))->
            setFilterableParameters(LaravelConfig::get($base_path . '.filterable', []))->
            setPaginationStatus(true)->
            setParameters(LaravelConfig::get($base_path . '.parameters', []))->
            setDynamicParameters($this->allowed_parameters)->
            setDescription('route-descriptions.resource_type_item_GET_index')->
            setAuthenticationStatus($this->permissions['view'])->
            option();

        return $this;
    }
}
