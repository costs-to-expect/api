<?php

declare(strict_types=1);

namespace App\HttpOptionResponse\ItemTransfer;

use App\HttpOptionResponse\Response;

class SimpleExpense extends Response
{
    public function create()
    {
        $get = new \App\HttpVerb\Get();
        $this->verbs['GET'] = $get->setDescription('route-descriptions.item_transfer_GET_show')->
            setAuthenticationStatus($this->permissions['view'])->
            option();

        return $this;
    }
}
