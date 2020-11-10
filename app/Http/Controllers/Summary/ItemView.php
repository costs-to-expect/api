<?php

namespace App\Http\Controllers\Summary;

use App\Entity\Item\Entity;
use App\Http\Controllers\Controller;
use App\Option\AllowedValue\ResourceItem;
use App\Option\SummaryItemCollection;
use App\Request\Parameter;
use App\Response\Responses;
use Illuminate\Http\JsonResponse;

class ItemView extends Controller
{
    public function index(string $resource_type_id, string $resource_id): JsonResponse
    {
        if ($this->viewAccessToResourceType((int)$resource_type_id) === false) {
            Responses::notFoundOrNotAccessible(trans('entities.resource'));
        }

        $entity = Entity::item((int)$resource_type_id);

        $parameters = Parameter\Request::fetch(
            array_keys($entity->summaryRequestParameters()),
            (int) $resource_type_id,
            (int) $resource_id
        );

        $search_parameters = Parameter\Search::fetch(
            $entity->summarySearchParameters()
        );

        $filter_parameters = Parameter\Filter::fetch(
            $entity->filterParameters()
        );

        $summary_class = $entity->summaryClass();
        $summary = new $summary_class(
            (int) $resource_type_id,
            (int) $resource_id,
            $parameters,
            $filter_parameters,
            $search_parameters,
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

        $defined_parameters = Parameter\Request::fetch(
            array_keys($entity->requestParameters()),
            (int) $resource_type_id,
            (int) $resource_id
        );

        $allowed_values = (new ResourceItem($entity))->allowedValues(
            $resource_type_id,
            $resource_id,
            $this->viewable_resource_types,
            $entity->requestParameters(),
            $defined_parameters
        );

        $response = new SummaryItemCollection($this->permissions((int) $resource_type_id));

        return $response->setEntity($entity)
            ->setAllowedValues($allowed_values)
            ->create()
            ->response();
    }
}
