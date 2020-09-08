<?php
declare(strict_types=1);

namespace App\Option;

class ItemTypeItem extends Response
{
    public function create()
    {
        $get = new \App\Option\Method\GetRequest();
        $this->verbs['GET'] = $get->setDescription('route-descriptions.item_type_GET_show')->
            setAuthenticationStatus($this->permissions['view'])->
            option();

        return $this;
    }
}
