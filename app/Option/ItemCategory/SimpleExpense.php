<?php
declare(strict_types=1);

namespace App\Option\ItemCategory;

use App\Option\Response;
use Illuminate\Support\Facades\Config;

class SimpleExpense extends Response
{
    public function create()
    {
        $get = new \App\HttpVerb\GetReponse();
        $this->verbs['GET'] = $get->setParameters(Config::get('api.item-category.parameters-show'))->
            setAuthenticationStatus($this->permissions['view'])->
            setDescription('route-descriptions.item_category_GET_show')->
            option();

        $delete = new \App\HttpVerb\DeleteResponse();
        $this->verbs['DELETE'] = $delete->setDescription('route-descriptions.item_category_DELETE')->
            setAuthenticationStatus($this->permissions['manage'])->
            setAuthenticationRequirement(true)->
            option();

        return $this;
    }
}
