<?php
declare(strict_types=1);

namespace App\HttpOptionResponse\ResourceTypeItem\Summary;

use App\HttpOptionResponse\Response;
use Illuminate\Support\Facades\Config as LaravelConfig;

class SimpleExpense extends Response
{
    public function create()
    {
        $base_path = 'api.resource-type-item-type-simple-expense';

        $get = new \App\HttpVerb\Get();
        $this->verbs['GET'] = $get->setSearchableParameters(LaravelConfig::get($base_path . '.summary-searchable', []))
            ->setParameters(LaravelConfig::get($base_path . '.summary-parameters', []))
            ->setFilterableParameters(LaravelConfig::get($base_path . '.summary-filterable', []))
            ->setDynamicParameters($this->allowed_parameters)
            ->setDescription('route-descriptions.summary-resource-type-item-GET-index')
            ->setAuthenticationStatus($this->permissions['view'])
            ->option();

        return $this;
    }
}
