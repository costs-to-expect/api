<?php
declare(strict_types=1);

namespace App\Option;

class CategoryItem extends Response
{
    public function create()
    {
        $get = new \App\Option\Method\Get();
        $this->verbs['GET'] = $get->setParameters('api.category.parameters.item')->
            setAuthenticationStatus($this->permissions['view'])->
            setDescription('route-descriptions.category_GET_show')->
            option();

        $delete = new \App\Option\Method\Delete();
        $this->verbs['DELETE'] = $delete->setAuthenticationRequirement(true)->
            setAuthenticationStatus($this->permissions['manage'])->
            setDescription('route-descriptions.category_DELETE')->
            option();

        $patch = new \App\Option\Method\Patch();
        $this->verbs['PATCH'] = $patch->setFields('api.category.fields-patch')->
            setAuthenticationRequirement(true)->
            setAuthenticationStatus($this->permissions['manage'])->
            setDescription('route-descriptions.category_PATCH')->
            option();

        return $this;
    }
}
