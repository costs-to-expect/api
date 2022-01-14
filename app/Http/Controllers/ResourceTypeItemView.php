<?php

namespace App\Http\Controllers;

use App\ItemType\Entity;
use App\Option\ResourceTypeItemCollection;
use Illuminate\Http\JsonResponse;

/**
 * View items for all resources for a resource type
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceTypeItemView extends Controller
{
    public function index(string $resource_type_id): JsonResponse
    {
        if ($this->viewAccessToResourceType((int) $resource_type_id) === false) {
            \App\Response\Responses::notFoundOrNotAccessible(trans('entities.resource-type'));
        }

        $entity = Entity::item((int) $resource_type_id);

        $collection_class = $entity->resourceTypeItemCollectionClass();

        /**
         * @var $collection \App\ItemType\ResourceTypeApiResponse
         */
        $collection = new $collection_class(
            (int) $resource_type_id,
            $this->writeAccessToResourceType($resource_type_id),
            $this->user_id
        );

        return $collection->response();
    }

    public function optionsIndex(string $resource_type_id): JsonResponse
    {
        if ($this->viewAccessToResourceType((int) $resource_type_id) === false) {
            \App\Response\Responses::notFoundOrNotAccessible(trans('entities.resource-type'));
        }

        $entity = Entity::item($resource_type_id);

        $response = new ResourceTypeItemCollection($this->permissions((int) $resource_type_id));

        return $response
            ->setEntity($entity)
            ->setAllowedParameters(
                $entity->allowedValuesForResourceTypeItemCollection(
                    $resource_type_id,
                    $this->viewable_resource_types
                )
            )
            ->create()
            ->response();
    }
}
