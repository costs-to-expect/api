<?php
declare(strict_types=1);

namespace App\Option\Item;

use App\ItemType\Game\Item;
use App\Option\Response;

class Game extends Response
{
    public function create()
    {
        $item = new Item();

        $get = new \App\Method\GetRequest();
        $this->verbs['GET'] = $get->setParameters($item->itemRequestParameters())
            ->setAuthenticationStatus($this->permissions['view'])
            ->setDescription('route-descriptions.item_GET_show')
            ->option();

        $delete = new \App\Method\DeleteRequest();
        $this->verbs['DELETE'] = $delete->setDescription('route-descriptions.item_DELETE')
            ->setAuthenticationStatus($this->permissions['manage'])
            ->setAuthenticationRequirement(true)
            ->option();

        $patch = new \App\Method\PatchRequest();
        $this->verbs['PATCH'] = $patch->setFields($item->patchFields())
            ->setDescription('route-descriptions.item_PATCH')
            ->setAuthenticationStatus($this->permissions['manage'])
            ->setAuthenticationRequirement(true)
            ->setDynamicFields($this->allowed_fields)
            ->option();

        return $this;
    }
}
