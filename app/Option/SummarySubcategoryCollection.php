<?php
declare(strict_types=1);

namespace App\Option;

use Illuminate\Support\Facades\Config;

class SummarySubcategoryCollection extends Response
{
    public function create()
    {
        $get = new \App\Option\Method\GetRequest();
        $this->verbs['GET'] = $get->setDescription('route-descriptions.summary_subcategory_GET_index')->
            setAuthenticationStatus($this->permissions['view'])->
            setSearchableParameters(Config::get('api.subcategory.summary-searchable'))->
            option();

        return $this;
    }
}
