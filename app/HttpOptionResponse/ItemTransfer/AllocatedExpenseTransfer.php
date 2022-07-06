<?php

declare(strict_types=1);

namespace App\HttpOptionResponse\ItemTransfer;

use App\HttpOptionResponse\Response;
use Illuminate\Support\Facades\Config;

class AllocatedExpenseTransfer extends Response
{
    public function create()
    {
        $post = new \App\HttpVerb\Post();
        $this->verbs['POST'] = $post->setFields(Config::get('api.item-transfer.fields-post'))->
            setAllowedValuesForFields($this->allowed_values_for_fields)->
            setDescription('route-descriptions.item_transfer_POST')->
            setAuthenticationStatus($this->permissions['manage'])->
            setAuthenticationRequirement(true)->
            option();

        return $this;
    }
}
