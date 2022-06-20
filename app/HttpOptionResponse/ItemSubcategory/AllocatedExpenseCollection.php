<?php
declare(strict_types=1);

namespace App\HttpOptionResponse\ItemSubcategory;

use App\HttpOptionResponse\Response;
use Illuminate\Support\Facades\Config;

class AllocatedExpenseCollection extends Response
{
    public function create()
    {
        $get = new \App\HttpVerb\Get();
        $this->verbs['GET'] = $get->setParameters(Config::get('api.item-subcategory.parameters'))->
            setAuthenticationStatus($this->permissions['view'])->
            setDescription('route-descriptions.item_sub_category_GET_index')->
            option();

        $post = new \App\HttpVerb\Post();
        $this->verbs['POST'] = $post->setFields(Config::get('api.item-subcategory.fields-post'))->
            setDynamicFields($this->allowed_fields)->
            setDescription('route-descriptions.item_sub_category_POST_allocated_expense')->
            setAuthenticationStatus($this->permissions['manage'])->
            setAuthenticationRequirement(true)->
            option();

        return $this;
    }
}
