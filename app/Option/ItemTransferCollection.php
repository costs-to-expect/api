<?php
declare(strict_types=1);

namespace App\Option;

class ItemTransferCollection extends Response
{
    public function create()
    {
        $get = new \App\Option\Method\Get();
        $this->verbs['GET'] = $get->setParameters('api.item-transfer.parameters.collection')->
            setPaginationStatus(true)->
            setAuthenticationStatus($this->permissions['view'])->
            setDescription('route-descriptions.item_transfer_GET_index')->
            option();

        return $this;
    }
}