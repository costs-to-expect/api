<?php
declare(strict_types=1);

namespace App\Option\ResourceTypeItem;

use App\ItemType\Game\Item;
use App\Option\Response;

class GameCollection extends Response
{
    public function create()
    {
        $item = new Item();

        $get = new \App\Method\GetRequest();
        $this->verbs['GET'] = $get->setSortableParameters($item->resourceTypeSortParameters())->
            setSearchableParameters($item->resourceTypeSearchParameters())->
            setFilterableParameters($item->resourceTypeFilterParameters())->
            setPaginationStatus(true)->
            setParameters($item->resourceTypeRequestParameters())->
            setDynamicParameters($this->allowed_parameters)->
            setDescription('route-descriptions.resource_type_item_GET_index')->
            setAuthenticationStatus($this->permissions['view'])->
            option();

        return $this;
    }
}
