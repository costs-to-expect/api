<?php
declare(strict_types=1);

namespace App\Option\Item\Summary;

use App\Option\Response;
use Illuminate\Support\Facades\Config as LaravelConfig;

class SimpleItem extends Response
{
    public function create()
    {
        $base_path = 'api.item-type-simple-item';

        $get = new \App\Method\GetRequest();
        $this->verbs['GET'] = $get
            ->setParameters(LaravelConfig::get($base_path . '.summary-parameters', []))
            ->setSearchableParameters(LaravelConfig::get($base_path . '.summary-searchable', []))
            ->setFilterableParameters(LaravelConfig::get($base_path . '.summary-filterable', []))
            ->setDynamicParameters($this->allowed_parameters)
            ->setDescription('route-descriptions.summary_GET_resource-type_resource_items')
            ->setAuthenticationStatus($this->permissions['view'])
            ->option();

        return $this;
    }
}
