<?php
declare(strict_types=1);

namespace App\Option;

use Illuminate\Support\Facades\Config;

class SummaryResourceCollection extends Response
{
    public function create()
    {
        $get = new \App\Option\Method\GetRequest();
        $this->verbs['GET'] = $get->setParameters(Config::get('api.resource.summary-parameters'))->
            setDescription('route-descriptions.summary-resource-GET-index')->
            setAuthenticationStatus($this->permissions['view'])->
            setSearchableParameters(Config::get('api.resource.summary-searchable'))->
            option();

        return $this;
    }
}
