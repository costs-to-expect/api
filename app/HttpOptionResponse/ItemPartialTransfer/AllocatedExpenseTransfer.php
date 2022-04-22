<?php
declare(strict_types=1);

namespace App\HttpOptionResponse\ItemPartialTransfer;

use App\HttpOptionResponse\Response;
use Illuminate\Support\Facades\Config;

class AllocatedExpenseTransfer extends Response
{
    public function create()
    {
        $post = new \App\HttpVerb\Post();
        $this->verbs['POST'] = $post->setFields(Config::get('api.item-partial-transfer.fields-post'))->
            setDynamicFields($this->allowed_fields)->
            setDescription('route-descriptions.item_partial_transfer_POST')->
            setAuthenticationStatus($this->permissions['manage'])->
            setAuthenticationRequirement(true)->
            option();

        return $this;
    }
}
