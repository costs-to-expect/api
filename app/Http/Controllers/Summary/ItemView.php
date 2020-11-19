<?php

namespace App\Http\Controllers\Summary;

use App\Http\Controllers\Controller;
use App\ItemType\Entity;
use App\Option\SummaryItemCollection;
use App\Response\Responses;
use Illuminate\Http\JsonResponse;

class ItemView extends Controller
{
    public function index(string $resource_type_id, string $resource_id): JsonResponse
    {
        if ($this->viewAccessToResourceType((int)$resource_type_id) === false) {
            Responses::notFoundOrNotAccessible(trans('entities.resource'));
        }

        $entity = Entity::item((int) $resource_type_id);

        $summary_class = $entity->summaryClass();
        $summary = new $summary_class(
            (int) $resource_type_id,
            (int) $resource_id,
            $this->writeAccessToResourceType($resource_type_id),
            $this->user_id
        );

        return $summary->response();
    }

    public function optionsIndex(string $resource_type_id, string $resource_id): JsonResponse
    {
        if ($this->viewAccessToResourceType((int) $resource_type_id) === false) {
            Responses::notFoundOrNotAccessible(trans('entities.resource'));
        }

        $entity = Entity::item($resource_type_id);

        $allowed_values = $entity->allowedValuesForItemCollection(
            (int) $resource_type_id,
            (int) $resource_id,
            $this->viewable_resource_types
        );

        $response = new SummaryItemCollection($this->permissions((int) $resource_type_id));

        return $response->setEntity($entity)
            ->setAllowedValues($allowed_values)
            ->create()
            ->response();
    }
}
