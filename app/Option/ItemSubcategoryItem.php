<?php
declare(strict_types=1);

namespace App\Option;

use Illuminate\Support\Facades\Config;

class ItemSubcategoryItem extends Response
{
    public function create()
    {
        $get = new \App\HttpVerb\GetReponse();
        $this->verbs['GET'] = $get->setParameters(Config::get('api.item-subcategory.parameters-show'))->
            setAuthenticationStatus($this->permissions['view'])->
            setDescription('route-descriptions.item_sub_category_GET_show')->
            option();

        $delete = new \App\HttpVerb\DeleteResponse();
        $this->verbs['DELETE'] = $delete->setDescription('route-descriptions.item_sub_category_DELETE')->
            setAuthenticationStatus($this->permissions['manage'])->
            setAuthenticationRequirement(true)->
            option();

        return $this;
    }
}
