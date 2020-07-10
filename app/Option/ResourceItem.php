<?php
declare(strict_types=1);

namespace App\Option;

class ResourceItem extends Response
{
    public function create()
    {
        $get = new \App\Option\Method\Get();
        $this->verbs['GET'] = $get->setParameters('api.resource.parameters.item')->
            setAuthenticationStatus($this->permissions['view'])->
            setDescription('route-descriptions.resource_GET_show')->
            option();

        $delete = new \App\Option\Method\Delete();
        $this->verbs['DELETE'] = $delete->setDescription('route-descriptions.resource_DELETE')->
            setAuthenticationRequirement(true)->
            setAuthenticationStatus($this->permissions['manage'])->
            option();

        $patch = new \App\Option\Method\Patch();
        $this->verbs['PATCH'] = $patch->setFields('api.resource.fields')->
            setDescription('route-descriptions.resource_PATCH')->
            setAuthenticationRequirement(true)->
            setAuthenticationStatus($this->permissions['manage'])->
            option();

        return $this;
    }
}
