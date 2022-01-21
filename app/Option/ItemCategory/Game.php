<?php
declare(strict_types=1);

namespace App\Option\ItemCategory;

use App\Option\Response;
use Illuminate\Support\Facades\Config;

class Game extends Response
{
    public function create()
    {
        $get = new \App\Method\GetRequest();
        $this->verbs['GET'] = $get->setParameters(Config::get('api.item-category.parameters.item'))->
            setAuthenticationStatus($this->permissions['view'])->
            setDescription('route-descriptions.item_category_GET_show')->
            option();

        $delete = new \App\Method\DeleteRequest();
        $this->verbs['DELETE'] = $delete->setDescription('route-descriptions.item_category_DELETE')->
            setAuthenticationStatus($this->permissions['manage'])->
            setAuthenticationRequirement(true)->
            option();

        return $this;
    }
}
