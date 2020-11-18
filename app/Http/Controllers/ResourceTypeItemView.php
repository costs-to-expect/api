<?php

namespace App\Http\Controllers;

use App\Entity\Item\Entity;
use App\Option\ResourceTypeItemCollection;
use App\Response\Cache;
use App\Request\Parameter;
use App\Response\Header\Headers;
use App\Response\Pagination as UtilityPagination;
use Illuminate\Http\JsonResponse;

/**
 * View items for all resources for a resource type
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceTypeItemView extends Controller
{
    /**
     * Return all the items based on the set filter options
     *
     * @param string $resource_type_id
     *
     * @return JsonResponse
     */
    public function index(string $resource_type_id): JsonResponse
    {
        if ($this->viewAccessToResourceType((int) $resource_type_id) === false) {
            \App\Response\Responses::notFoundOrNotAccessible(trans('entities.resource-type'));
        }

        $entity = Entity::item((int) $resource_type_id);

        $collection_class = $entity->resourceTypeItemCollectionClass();
        $collection = new $collection_class(
            (int) $resource_type_id,
            $this->writeAccessToResourceType($resource_type_id),
            $this->user_id
        );

        return $collection->response();
    }

    /**
     * Generate the OPTIONS request for the items list
     *
     * @param string $resource_type_id
     *
     * @return JsonResponse
     */
    public function optionsIndex(string $resource_type_id): JsonResponse
    {
        if ($this->viewAccessToResourceType((int) $resource_type_id) === false) {
            \App\Response\Responses::notFoundOrNotAccessible(trans('entities.resource-type'));
        }

        $entity = Entity::item($resource_type_id);

        $response = new ResourceTypeItemCollection($this->permissions((int) $resource_type_id));

        return $response
            ->setEntity($entity)
            ->setAllowedValues(
                $entity->allowedValuesForResourceTypeItemCollection(
                    $resource_type_id,
                    $this->viewable_resource_types
                )
            )
            ->create()
            ->response();
    }
}
