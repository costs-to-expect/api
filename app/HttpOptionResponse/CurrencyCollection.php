<?php
declare(strict_types=1);

namespace App\HttpOptionResponse;

use Illuminate\Support\Facades\Config;

class CurrencyCollection extends Response
{
    public function create()
    {
        $get = new \App\HttpVerb\Get();
        $this->verbs['GET'] = $get->setSortableParameters(Config::get('api.currency.sortable'))->
            setSearchableParameters(Config::get('api.currency.searchable'))->
            setPaginationStatus(true, true)->
            setDescription('route-descriptions.currency_GET_index')->
            setAuthenticationStatus($this->permissions['view'])->
            option();

        return $this;
    }
}
