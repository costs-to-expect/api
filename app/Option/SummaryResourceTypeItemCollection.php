<?php
declare(strict_types=1);

namespace App\Option;

class SummaryResourceTypeItemCollection extends Response
{
    public function create()
    {
        $get = new \App\Option\Method\GetRequest();
        $this->verbs['GET'] = $get->setSearchableParameters($this->entity->summaryResourceTypeSearchParameters())
            ->setParameters($this->entity->summaryResourceTypeRequestParameters())
            ->setFilterableParameters($this->entity->summaryResourceTypeFilterParameters())
            ->setDynamicParameters($this->allowed_values)
            ->setDescription('route-descriptions.summary-resource-type-item-GET-index')
            ->setAuthenticationStatus($this->permissions['view'])
            ->option();

        return $this;
    }
}
