<?php
declare(strict_types=1);

namespace App\HttpOptionResponse;

class ItemSubtypeItem extends Response
{
    public function create()
    {
        $get = new \App\HttpVerb\Get();
        $this->verbs['GET'] = $get
            ->setDescription('route-descriptions.item_subtype_GET_show')
            ->setAuthenticationStatus($this->permissions['view'])
            ->option();

        return $this;
    }
}
