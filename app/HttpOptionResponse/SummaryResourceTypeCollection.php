<?php
declare(strict_types=1);

namespace App\HttpOptionResponse;

use Illuminate\Support\Facades\Config;

class SummaryResourceTypeCollection extends Response
{
    public function create()
    {
        $get = new \App\HttpVerb\Get();
        $this->verbs['GET'] = $get->setParameters(Config::get('api.resource-type.summary-parameters'))->
            setDescription('route-descriptions.summary_resource_type_GET_index')->
            setAuthenticationStatus($this->permissions['view'])->
            setSearchableParameters(Config::get('api.resource-type.summary-searchable'))->
            option();

        return $this;
    }
}
