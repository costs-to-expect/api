<?php
declare(strict_types=1);

namespace App\Option\Summary\Item;

use App\Entity\Item\Entity;
use App\Option\SummaryItemCollection;
use App\Request\Parameter\Request;

class AllocatedExpense
{
    public function __construct(
        int $resource_type_id,
        int $resource_id,
        array $viewable_resource_types,
        array $permissions
    )
    {
        $entity = Entity::item($resource_type_id);

        $request_parameters = $entity->requestParameters();

        $defined_parameters = Request::fetch(
            array_keys($request_parameters),
            $resource_type_id,
            $resource_id
        );

        $allowed_values = new \App\AllowedValue\Item\AllocatedExpense(
            $resource_type_id,
            $resource_id,
            $viewable_resource_types
        );

        $values = $allowed_values
            ->setParameters(
                $request_parameters,
                $defined_parameters
            )
            ->fetch()
            ->allowedValues();

        $response = new SummaryItemCollection($permissions);

        return $response->setEntity($entity)
            ->setAllowedValues($values)
            ->create()
            ->response();
    }

    public function response()
    {
        $response = new SummaryItemCollection($permissions);

        return $response->setEntity($entity)
            ->setAllowedValues($values)
            ->create()
            ->response();
    }
}
