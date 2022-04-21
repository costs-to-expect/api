<?php
declare(strict_types=1);

namespace App\Option\ItemPartialTransfer;

use App\Option\Response;
use Illuminate\Support\Facades\Config;

class AllocatedExpenseCollection extends Response
{
    public function create()
    {
        $get = new \App\Method\GetRequest();
        $this->verbs['GET'] = $get->setParameters(Config::get('api.item-partial-transfer.parameters'))->
            setPaginationStatus(true)->
            setAuthenticationStatus($this->permissions['view'])->
            setDescription('route-descriptions.item_partial_transfer_GET_index')->
            option();

        return $this;
    }
}
