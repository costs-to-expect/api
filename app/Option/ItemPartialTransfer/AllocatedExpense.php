<?php
declare(strict_types=1);

namespace App\Option\ItemPartialTransfer;

use App\Option\Response;

class AllocatedExpense extends Response
{
    public function create()
    {
        $get = new \App\HttpVerb\GetReponse();
        $this->verbs['GET'] = $get->setDescription('route-descriptions.item_partial_transfer_GET_show')->
            setAuthenticationStatus($this->permissions['view'])->
            option();

        $delete = new \App\HttpVerb\DeleteResponse();
        $this->verbs['DELETE'] = $delete->setAuthenticationRequirement(true)->
            setAuthenticationStatus($this->permissions['manage'])->
            setDescription('route-descriptions.item_partial_transfer_DELETE')->
            option();

        return $this;
    }
}
