<?php
declare(strict_types=1);

namespace App\Option;

class ItemSubtypeItem extends Response
{
    public function create()
    {
        $get = new \App\HttpVerb\GetReponse();
        $this->verbs['GET'] = $get
            ->setDescription('route-descriptions.item_subtype_GET_show')
            ->setAuthenticationStatus($this->permissions['view'])
            ->option();

        return $this;
    }
}
