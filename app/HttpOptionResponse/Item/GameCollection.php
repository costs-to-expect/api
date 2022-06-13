<?php
declare(strict_types=1);

namespace App\HttpOptionResponse\Item;

use App\HttpOptionResponse\Response;
use Illuminate\Support\Facades\Config as LaravelConfig;

class GameCollection extends Response
{
    public function create()
    {
        $base_path = 'api.item-type-game';

        $get = new \App\HttpVerb\Get();
        $this->verbs['GET'] = $get->setSortableParameters(LaravelConfig::get($base_path . '.sortable', []))
            ->setSearchableParameters(LaravelConfig::get($base_path . '.searchable', []))
            ->setFilterableParameters(LaravelConfig::get($base_path . '.filterable', []))
            ->setParameters(LaravelConfig::get($base_path . '.parameters', []))
            ->setDynamicParameters($this->allowed_parameters)
            ->setPaginationStatus(true)
            ->setAuthenticationStatus($this->permissions['view'])
            ->setDescription('route-descriptions.item_GET_index')
            ->option();

        $post = new \App\HttpVerb\Post();
        $this->verbs['POST'] = $post->setFields(LaravelConfig::get($base_path . '.fields-post', []))
            ->setDescription( 'route-descriptions.item_POST')
            ->setAuthenticationRequirement(true)
            ->setAuthenticationStatus($this->permissions['manage'])
            ->setDynamicFields($this->allowed_fields)
            ->option();

        return $this;
    }
}
