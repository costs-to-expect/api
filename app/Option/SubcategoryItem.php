<?php
declare(strict_types=1);

namespace App\Option;

class SubcategoryItem extends Response
{
    public function create()
    {
        $get = new \App\Option\Method\Get();
        $this->verbs['GET'] = $get->setParameters('api.subcategory.parameters.item')->
            setDescription('route-descriptions.sub_category_GET_show')->
            setAuthenticationStatus($this->permissions['view'])->
            option();

        $delete = new \App\Option\Method\Delete();
        $this->verbs['DELETE'] = $delete->setDescription('route-descriptions.sub_category_DELETE')->
            setAuthenticationRequirement(true)->
            setAuthenticationStatus($this->permissions['manage'])->
            option();

        $patch = new \App\Option\Method\Patch();
        $this->verbs['PATCH'] = $patch->setFields('api.subcategory.fields')->
            setDescription('route-descriptions.sub_category_PATCH')->
            setAuthenticationRequirement(true)->
            setAuthenticationStatus($this->permissions['manage'])->
            option();

        return $this;
    }
}
