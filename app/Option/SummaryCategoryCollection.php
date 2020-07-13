<?php
declare(strict_types=1);

namespace App\Option;

class SummaryCategoryCollection extends Response
{
    public function create()
    {
        $get = new \App\Option\Method\Get();
        $this->verbs['GET'] = $get->setDescription('route-descriptions.summary_category_GET_index')->
            setAuthenticationStatus($this->permissions['view'])->
            setSearchableParameters('api.category.summary-searchable')->
            option();

        return $this;
    }
}