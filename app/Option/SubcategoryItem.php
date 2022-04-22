<?php
declare(strict_types=1);

namespace App\Option;

use Illuminate\Support\Facades\Config;

class SubcategoryItem extends Response
{
    public function create()
    {
        $get = new \App\HttpVerb\GetReponse();
        $this->verbs['GET'] = $get->setParameters(Config::get('api.subcategory.parameters-show'))->
            setDescription('route-descriptions.sub_category_GET_show')->
            setAuthenticationStatus($this->permissions['view'])->
            option();

        $delete = new \App\HttpVerb\DeleteResponse();
        $this->verbs['DELETE'] = $delete->setDescription('route-descriptions.sub_category_DELETE')->
            setAuthenticationRequirement(true)->
            setAuthenticationStatus($this->permissions['manage'])->
            option();

        $patch = new \App\HttpVerb\PatchResponse();
        $this->verbs['PATCH'] = $patch->setFields(Config::get('api.subcategory.fields-post'))->
            setDescription('route-descriptions.sub_category_PATCH')->
            setAuthenticationRequirement(true)->
            setAuthenticationStatus($this->permissions['manage'])->
            option();

        return $this;
    }
}
