<?php
declare(strict_types=1);

namespace App\Option;

use Illuminate\Support\Facades\Config;

class QueueCollection extends Response
{
    public function create()
    {
        $get = new \App\Option\Method\GetRequest();
        $this->verbs['GET'] = $get->setPaginationStatus(true, true)->
            setDescription('route-descriptions.queue_GET_index')->
            setAuthenticationStatus($this->permissions['view'])->
            option();

        return $this;
    }
}
