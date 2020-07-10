<?php
declare(strict_types=1);

namespace App\Option;

class ResourceTypeItem extends Response
{
    public function create()
    {
        $get = new \App\Option\Method\Get();
        $this->verbs['GET'] = $get->setParameters('api.resource-type.parameters.item')->
            setDescription('route-descriptions.resource_type_GET_show')->
            setAuthenticationStatus($this->permissions['view'])->
            option();

        $delete = new \App\Option\Method\Delete();
        $this->verbs['DELETE'] = $delete->setDescription('route-descriptions.resource_type_DELETE')->
            setAuthenticationRequirement(true)->
            setAuthenticationStatus($this->permissions['manage'])->
            option();

        $patch = new \App\Option\Method\Patch();
        $this->verbs['PATCH'] = $patch->setFields('api.resource-type.fields-patch')->
            setDescription('route-descriptions.resource_type_PATCH')->
            setAuthenticationRequirement(true)->
            setAuthenticationStatus($this->permissions['manage'])->option();

        return $this;
    }
}
