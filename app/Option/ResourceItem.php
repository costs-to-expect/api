<?php
declare(strict_types=1);

namespace App\Option;

use Illuminate\Support\Facades\Config;

class ResourceItem extends Response
{
    public function create()
    {
        $get = new \App\HttpVerb\GetReponse();
        $this->verbs['GET'] = $get->setParameters(Config::get('api.resource.parameters-show'))->
            setAuthenticationStatus($this->permissions['view'])->
            setDescription('route-descriptions.resource_GET_show')->
            option();

        $delete = new \App\HttpVerb\DeleteResponse();
        $this->verbs['DELETE'] = $delete->setDescription('route-descriptions.resource_DELETE')->
            setAuthenticationRequirement(true)->
            setAuthenticationStatus($this->permissions['manage'])->
            option();

        $patch = new \App\HttpVerb\PatchResponse();
        $this->verbs['PATCH'] = $patch->setFields(Config::get('api.resource.fields-patch'))->
            setDescription('route-descriptions.resource_PATCH')->
            setAuthenticationRequirement(true)->
            setAuthenticationStatus($this->permissions['manage'])->
            option();

        return $this;
    }
}
