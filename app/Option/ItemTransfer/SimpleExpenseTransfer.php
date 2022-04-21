<?php
declare(strict_types=1);

namespace App\Option\ItemTransfer;

use App\Option\Response;
use Illuminate\Support\Facades\Config;

class SimpleExpenseTransfer extends Response
{
    public function create()
    {
        $post = new \App\Method\PostRequest();
        $this->verbs['POST'] = $post->setFields(Config::get('api.item-transfer.fields-post'))->
            setDynamicFields($this->allowed_fields)->
            setDescription('route-descriptions.item_transfer_POST')->
            setAuthenticationStatus($this->permissions['manage'])->
            setAuthenticationRequirement(true)->
            option();

        return $this;
    }
}
