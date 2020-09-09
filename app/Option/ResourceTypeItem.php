<?php
declare(strict_types=1);

namespace App\Option;

use Illuminate\Support\Facades\Config;

class ResourceTypeItem extends Response
{
    public function create()
    {
        $get = new \App\Option\Method\GetRequest();
        $this->verbs['GET'] = $get->setParameters(Config::get('api.resource-type.parameters.item'))->
            setDescription('route-descriptions.resource_type_GET_show')->
            setAuthenticationStatus($this->permissions['view'])->
            option();

        $delete = new \App\Option\Method\DeleteRequest();
        $this->verbs['DELETE'] = $delete->setDescription('route-descriptions.resource_type_DELETE')->
            setAuthenticationRequirement(true)->
            setAuthenticationStatus($this->permissions['manage'])->
            option();

        $patch = new \App\Option\Method\PatchRequest();
        $this->verbs['PATCH'] = $patch->setFields(Config::get('api.resource-type.fields-patch'))->
            setDescription('route-descriptions.resource_type_PATCH')->
            setAuthenticationRequirement(true)->
            setAuthenticationStatus($this->permissions['manage'])->option();

        return $this;
    }
}
