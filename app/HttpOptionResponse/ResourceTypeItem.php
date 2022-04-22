<?php
declare(strict_types=1);

namespace App\HttpOptionResponse;

use Illuminate\Support\Facades\Config;

class ResourceTypeItem extends Response
{
    public function create()
    {
        $get = new \App\HttpVerb\Get();
        $this->verbs['GET'] = $get->setParameters(Config::get('api.resource-type.parameters-show'))->
            setDescription('route-descriptions.resource_type_GET_show')->
            setAuthenticationStatus($this->permissions['view'])->
            option();

        $delete = new \App\HttpVerb\Delete();
        $this->verbs['DELETE'] = $delete->setDescription('route-descriptions.resource_type_DELETE')->
            setAuthenticationRequirement(true)->
            setAuthenticationStatus($this->permissions['manage'])->
            option();

        $patch = new \App\HttpVerb\Patch();
        $this->verbs['PATCH'] = $patch->setFields(Config::get('api.resource-type.fields-patch'))->
            setDescription('route-descriptions.resource_type_PATCH')->
            setAuthenticationRequirement(true)->
            setAuthenticationStatus($this->permissions['manage'])->option();

        return $this;
    }
}
