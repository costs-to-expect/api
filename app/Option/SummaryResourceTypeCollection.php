<?php
declare(strict_types=1);

namespace App\Option;

use Illuminate\Support\Facades\Config;

class SummaryResourceTypeCollection extends Response
{
    public function create()
    {
        $get = new \App\Method\GetRequest();
        $this->verbs['GET'] = $get->setParameters(Config::get('api.resource-type.summary-parameters'))->
            setDescription('route-descriptions.summary-resource-type-GET-index')->
            setAuthenticationStatus($this->permissions['view'])->
            setSearchableParameters(Config::get('api.resource-type.summary-searchable'))->
            option();

        return $this;
    }
}
