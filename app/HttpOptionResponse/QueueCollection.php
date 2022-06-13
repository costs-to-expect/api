<?php
declare(strict_types=1);

namespace App\HttpOptionResponse;

class QueueCollection extends Response
{
    public function create()
    {
        $get = new \App\HttpVerb\Get();
        $this->verbs['GET'] = $get->setPaginationStatus(true, true)->
            setDescription('route-descriptions.queue_GET_index')->
            setAuthenticationStatus($this->permissions['view'])->
            option();

        return $this;
    }
}
