<?php

namespace App\Http\Controllers\Summary;

use App\Entity\Item\Entity;
use App\Http\Controllers\Controller;
use App\Option\SummaryResourceTypeItemCollection;
use App\Request\Parameter;
use App\Response\Responses;
use Illuminate\Http\JsonResponse;

/**
 * Summary for resource type items route
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceTypeItemView extends Controller
{
    public function index(string $resource_type_id): JsonResponse
    {
        if ($this->viewAccessToResourceType((int)$resource_type_id) === false) {
            Responses::notFoundOrNotAccessible(trans('entities.resource'));
        }

        $entity = Entity::item((int)$resource_type_id);

        $summary_class = $entity->resourceTypeSummaryClass();
        $summary = new $summary_class(
            (int) $resource_type_id,
            $this->writeAccessToResourceType($resource_type_id),
            $this->user_id
        );

        return $summary->response();
    }

    public function optionsIndex(string $resource_type_id): JsonResponse
    {
        if ($this->viewAccessToResourceType((int) $resource_type_id) === false) {
            \App\Response\Responses::notFoundOrNotAccessible(trans('entities.resource-type'));
        }

        $entity = Entity::item($resource_type_id);

        $defined_parameters = Parameter\Request::fetch(
            array_keys($entity->resourceTypeRequestParameters()),
            $resource_type_id
        );

        $allowed_values = (new \App\Option\AllowedValue\ResourceTypeItem($entity))->allowedValues(
            $resource_type_id,
            $this->viewable_resource_types,
            $entity->resourceTypeRequestParameters(),
            $defined_parameters
        );

        $response = new SummaryResourceTypeItemCollection($this->permissions((int) $resource_type_id));

        return $response->setEntity($entity)
            ->setAllowedValues($allowed_values)
            ->create()
            ->response();
    }
}
