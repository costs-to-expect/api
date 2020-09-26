<?php
declare(strict_types=1);

namespace App\Option;

class QueueItem extends Response
{
    public function create()
    {
        $get = new \App\Option\Method\GetRequest();
        $this->verbs['GET'] = $get->setDescription('route-descriptions.queue_GET_show')->
            setAuthenticationStatus($this->permissions['view'])->
            option();

        return $this;
    }
}
