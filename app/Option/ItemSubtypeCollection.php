<?php
declare(strict_types=1);

namespace App\Option;

use Illuminate\Support\Facades\Config;

class ItemSubtypeCollection extends Response
{
    public function create()
    {
        $get = new \App\Option\Method\GetRequest();
        $this->verbs['GET'] = $get
            ->setSortableParameters(Config::get('api.item-subtype.sortable'))
            ->setSearchableParameters(Config::get('api.item-subtype.searchable'))
            ->setPaginationStatus(true, true)
            ->setDescription('route-descriptions.item_subtype_GET_index')
            ->setAuthenticationStatus($this->permissions['view'])
            ->option();

        return $this;
    }
}
