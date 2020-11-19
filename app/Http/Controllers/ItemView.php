<?php

namespace App\Http\Controllers;

use App\Entity\Item\Entity;
use App\Http\Controllers\Item\Item;
use App\Option\ItemCollection;
use App\Option\ItemItem;
use App\Response\Header\Header;
use App\Request\Parameter;
use Illuminate\Http\JsonResponse;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemView extends Controller
{
    /**
     * Return all the items for the resource type and resource applying
     * any filtering, pagination and ordering
     *
     * @param string $resource_type_id
     * @param string $resource_id
     *
     * @return JsonResponse
     */
    public function index(
        string $resource_type_id,
        string $resource_id
    ): JsonResponse
    {
        if ($this->viewAccessToResourceType((int) $resource_type_id) === false) {
            \App\Response\Responses::notFoundOrNotAccessible(trans('entities.resource'));
        }

        $entity = Entity::item((int) $resource_type_id);

        $collection_class = $entity->itemCollectionClass();

        /**
         * @var $collection Item
         */
        $collection = new $collection_class(
            (int) $resource_type_id,
            (int) $resource_id,
            $this->writeAccessToResourceType($resource_type_id),
            $this->user_id
        );

        return $collection->collectionResponse();
    }

    /**
     * Return a single item
     *
     * @param string $resource_id
     * @param string $resource_type_id
     * @param string $item_id
     *
     * @return JsonResponse
     */
    public function show(
        $resource_type_id,
        $resource_id,
        $item_id
    ): JsonResponse
    {
        if ($this->viewAccessToResourceType((int) $resource_type_id) === false) {
            \App\Response\Responses::notFoundOrNotAccessible(trans('entities.item'));
        }

        $entity = Entity::item($resource_type_id);
        $collection_class = $entity->itemCollectionClass();

        /**
         * @var $collection Item
         */
        $collection = new $collection_class(
            (int) $resource_type_id,
            (int) $resource_id,
            $this->writeAccessToResourceType($resource_type_id),
            $this->user_id
        );

        return $collection->showResponse($item_id);
    }

    /**
     * Generate the OPTIONS request for the item list
     *
     * @param string $resource_type_id
     * @param string $resource_id
     *
     * @return JsonResponse
     */
    public function optionsIndex(
        string $resource_type_id,
        string $resource_id
    ): JsonResponse
    {
        if ($this->viewAccessToResourceType((int) $resource_type_id) === false) {
            \App\Response\Responses::notFoundOrNotAccessible(trans('entities.resource'));
        }

        $entity = Entity::item($resource_type_id);

        $response = new ItemCollection($this->permissions((int) $resource_type_id));

        return $response
            ->setEntity($entity)
            ->setAllowedValues(
                $entity->allowedValuesForItemCollection(
                    $resource_type_id,
                    $resource_id,
                    $this->viewable_resource_types
                )
            )
            ->create()
            ->response();
    }

    /**
     * Generate the OPTIONS request for a specific item
     *
     * @param string $resource_id
     * @param string $resource_type_id
     * @param string $item_id
     *
     * @return JsonResponse
     */
    public function optionsShow(
        string $resource_type_id,
        string $resource_id,
        string $item_id
    ): JsonResponse
    {
        if ($this->viewAccessToResourceType((int) $resource_type_id) === false) {
            \App\Response\Responses::notFoundOrNotAccessible(trans('entities.item'));
        }

        $entity = Entity::item($resource_type_id);

        $item_model = $entity->model();

        $item = $item_model->single($resource_type_id, $resource_id, $item_id);

        if ($item === null) {
            return \App\Response\Responses::notFound(trans('entities.item'));
        }

        $response = new ItemItem($this->permissions((int) $resource_type_id));

        return $response
            ->setEntity($entity)
            ->setAllowedValues($entity->allowedValuesForItem((int) $resource_type_id))
            ->create()
            ->response();
    }
}
