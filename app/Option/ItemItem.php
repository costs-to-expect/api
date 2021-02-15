<?php
declare(strict_types=1);

namespace App\Option;

class ItemItem extends Response
{
    public function create()
    {
        $get = new \App\Method\GetRequest();
        $this->verbs['GET'] = $get->setParameters($this->entity->itemRequestParameters())
            ->setAuthenticationStatus($this->permissions['view'])
            ->setDescription('route-descriptions.item_GET_show')
            ->option();

        $delete = new \App\Method\DeleteRequest();
        $this->verbs['DELETE'] = $delete->setDescription('route-descriptions.item_DELETE')
            ->setAuthenticationStatus($this->permissions['manage'])
            ->setAuthenticationRequirement(true)
            ->option();

        $patch = new \App\Method\PatchRequest();
        $this->verbs['PATCH'] = $patch->setFields($this->entity->patchFields())
            ->setDescription('route-descriptions.item_PATCH')
            ->setAuthenticationStatus($this->permissions['manage'])
            ->setAuthenticationRequirement(true)
            ->setDynamicFields($this->allowed_fields)
            ->option();

        return $this;
    }
}
