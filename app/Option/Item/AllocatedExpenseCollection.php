<?php
declare(strict_types=1);

namespace App\Option\Item;

use App\ItemType\AllocatedExpense\Item;
use App\Option\Response;

class AllocatedExpenseCollection extends Response
{
    public function create()
    {
        $item = new Item();

        $get = new \App\Method\GetRequest();
        $this->verbs['GET'] = $get->setSortableParameters($item->sortParameters())
            ->setSearchableParameters($item->searchParameters())
            ->setFilterableParameters($item->filterParameters())
            ->setParameters($item->requestParameters())
            ->setDynamicParameters($this->allowed_parameters)
            ->setPaginationStatus(true)
            ->setAuthenticationStatus($this->permissions['view'])
            ->setDescription('route-descriptions.item_GET_index')
            ->option();

        $post = new \App\Method\PostRequest();
        $this->verbs['POST'] = $post->setFields($item->postFields())
            ->setDescription( 'route-descriptions.item_POST')
            ->setAuthenticationRequirement(true)
            ->setAuthenticationStatus($this->permissions['manage'])
            ->setDynamicFields($this->allowed_fields)
            ->option();

        return $this;
    }
}
