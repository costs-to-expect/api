<?php

declare(strict_types=1);

namespace App\HttpOptionResponse;

use Illuminate\Support\Facades\Config;

class SummaryCategoryCollection extends Response
{
    public function create()
    {
        $get = new \App\HttpVerb\Get();
        $this->verbs['GET'] = $get->setDescription('route-descriptions.summary_category_GET_index')->
            setAuthenticationStatus($this->permissions['view'])->
            setSearchableParameters(Config::get('api.category.summary-searchable'))->
            option();

        return $this;
    }
}
