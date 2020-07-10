<?php
declare(strict_types=1);

namespace App\Option;

class ItemTypeCollection extends Response
{
    public function create()
    {
        $get = new \App\Option\Method\Get();
        $this->verbs['GET'] = $get->setSortableParameters('api.item-type.sortable')->
            setSearchableParameters('api.item-type.searchable')->
            setPaginationStatus(true, true)->
            setDescription('route-descriptions.item_type_GET_index')->
            setAuthenticationStatus($this->permissions['view'])->
            option();

        return $this;
    }
}
