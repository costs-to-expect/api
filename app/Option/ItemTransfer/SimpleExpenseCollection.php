<?php
declare(strict_types=1);

namespace App\Option\ItemTransfer;

use App\Option\Response;
use Illuminate\Support\Facades\Config;

class SimpleExpenseCollection extends Response
{
    public function create()
    {
        $get = new \App\Method\GetRequest();
        $this->verbs['GET'] = $get->setParameters(Config::get('api.item-transfer.parameters.collection'))->
            setPaginationStatus(true)->
            setAuthenticationStatus($this->permissions['view'])->
            setDescription('route-descriptions.item_transfer_GET_index')->
            option();

        return $this;
    }
}