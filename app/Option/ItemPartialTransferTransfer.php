<?php
declare(strict_types=1);

namespace App\Option;

class ItemPartialTransferTransfer extends Response
{
    public function create()
    {
        $post = new \App\Option\Method\Post();
        $this->verbs['POST'] = $post->setFields('api.item-partial-transfer.fields')->
            setDynamicFields($this->allowed_values)->
            setDescription('route-descriptions.item_partial_transfer_POST')->
            setAuthenticationStatus($this->permissions['manage'])->
            setAuthenticationRequirement(true)->
            option();

        return $this;
    }
}
