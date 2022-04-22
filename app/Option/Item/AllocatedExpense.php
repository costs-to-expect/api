<?php
declare(strict_types=1);

namespace App\Option\Item;

use App\Option\Response;
use Illuminate\Support\Facades\Config as LaravelConfig;

class AllocatedExpense extends Response
{
    public function create()
    {
        $base_path = 'api.item-type-allocated-expense';

        $get = new \App\HttpVerb\GetReponse();
        $this->verbs['GET'] = $get->setParameters(LaravelConfig::get($base_path . '.parameters-show', []))
            ->setAuthenticationStatus($this->permissions['view'])
            ->setDescription('route-descriptions.item_GET_show')
            ->option();

        $delete = new \App\HttpVerb\DeleteResponse();
        $this->verbs['DELETE'] = $delete->setDescription('route-descriptions.item_DELETE')
            ->setAuthenticationStatus($this->permissions['manage'])
            ->setAuthenticationRequirement(true)
            ->option();

        $patch = new \App\HttpVerb\PatchResponse();
        $this->verbs['PATCH'] = $patch->setFields(LaravelConfig::get($base_path . '.fields-post', []))
            ->setDescription('route-descriptions.item_PATCH')
            ->setAuthenticationStatus($this->permissions['manage'])
            ->setAuthenticationRequirement(true)
            ->setDynamicFields($this->allowed_fields)
            ->option();

        return $this;
    }
}
