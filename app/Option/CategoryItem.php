<?php
declare(strict_types=1);

namespace App\Option;

use Illuminate\Support\Facades\Config;

class CategoryItem extends Response
{
    public function create()
    {
        $get = new \App\HttpVerb\GetReponse();
        $this->verbs['GET'] = $get->setParameters(Config::get('api.category.parameters-show'))->
            setAuthenticationStatus($this->permissions['view'])->
            setDescription('route-descriptions.category_GET_show')->
            option();

        $delete = new \App\HttpVerb\DeleteResponse();
        $this->verbs['DELETE'] = $delete->setAuthenticationRequirement(true)->
            setAuthenticationStatus($this->permissions['manage'])->
            setDescription('route-descriptions.category_DELETE')->
            option();

        $patch = new \App\HttpVerb\PatchResponse();
        $this->verbs['PATCH'] = $patch->setFields(Config::get('api.category.fields-patch'))->
            setAuthenticationRequirement(true)->
            setAuthenticationStatus($this->permissions['manage'])->
            setDescription('route-descriptions.category_PATCH')->
            option();

        return $this;
    }
}
