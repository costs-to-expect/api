<?php
declare(strict_types=1);

namespace App\HttpOptionResponse\ItemTransfer;

use App\HttpOptionResponse\Response;
use Illuminate\Support\Facades\Config;

class SimpleExpenseCollection extends Response
{
    public function create()
    {
        $get = new \App\HttpVerb\Get();
        $this->verbs['GET'] = $get->setParameters(Config::get('api.item-transfer.parameters'))->
            setPaginationStatus(true)->
            setAuthenticationStatus($this->permissions['view'])->
            setDescription('route-descriptions.item_transfer_GET_index')->
            option();

        return $this;
    }
}
