<?php
declare(strict_types=1);

namespace App\Option;

use Illuminate\Support\Facades\Config;

class PermittedUserCollection extends Response
{
    public function create()
    {
        $get = new \App\HttpVerb\GetReponse();
        $this->verbs['GET'] = $get->setSortableParameters(Config::get('api.permitted-user.sortable'))->
            setSearchableParameters(Config::get('api.permitted-user.searchable'))->
            setPaginationStatus(true, true)->
            setAuthenticationStatus($this->permissions['view'])->
            setDescription('route-descriptions.permitted_user_GET_index')->
            option();

        $post = new \App\HttpVerb\PostResponse();
        $this->verbs['POST'] = $post->setFields(Config::get('api.permitted-user.fields-post'))->
            setDescription('route-descriptions.permitted_user_POST')->
            setAuthenticationStatus($this->permissions['manage'])->
            setAuthenticationRequirement(true)->
            option();

        return $this;
    }
}
