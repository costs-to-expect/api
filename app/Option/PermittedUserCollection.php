<?php
declare(strict_types=1);

namespace App\Option;

class PermittedUserCollection extends Response
{
    public function create()
    {
        $get = new \App\Option\Method\Get();
        $this->verbs['GET'] = $get->setSortableParameters('api.permitted-user.sortable')->
            setSortableParameters('api.permitted-user.searchable')->
            setPaginationStatus(true, true)->
            setAuthenticationStatus($this->permissions['view'])->
            setDescription('route-descriptions.permitted_user_GET_index')->
            option();

        $post = new \App\Option\Method\Post();
        $this->verbs['POST'] = $post->setFields('api.permitted-user.fields')->
            setDescription('route-descriptions.permitted_user_POST')->
            setAuthenticationStatus($this->permissions['manage'])->
            setAuthenticationRequirement(true)->
            option();

        return $this;
    }
}
