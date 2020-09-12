<?php
declare(strict_types=1);

namespace App\Option;

class SummaryItemCollection extends Response
{
    public function create()
    {
        $get = new \App\Option\Method\GetRequest();
        $this->verbs['GET'] = $get->setParameters($this->entity->summaryRequestParameters())
            ->setSearchableParameters($this->entity->summarySearchParameters())
            ->setFilterableParameters($this->entity->summaryFilterParameters())
            ->setDynamicParameters($this->allowed_values)
            ->setDescription('route-descriptions.summary_GET_resource-type_resource_items')
            ->setAuthenticationStatus($this->permissions['view'])
            ->option();

        return $this;
    }
}
