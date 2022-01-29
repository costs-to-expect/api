<?php
declare(strict_types=1);

namespace App\Option\ItemTransfer;

use App\Option\Response;

class SimpleExpense extends Response
{
    public function create()
    {
        $get = new \App\Method\GetRequest();
        $this->verbs['GET'] = $get->setDescription('route-descriptions.item_transfer_GET_show')->
            setAuthenticationStatus($this->permissions['view'])->
            option();

        return $this;
    }
}
