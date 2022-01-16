<?php

namespace App\Http\Controllers;

use App\AllowedValue\Currency;
use App\ItemType\Entity;
use App\Option\ItemCollection;
use App\Option\ItemItem;
use Illuminate\Http\JsonResponse;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemView extends Controller
{
    public function index(
        string $resource_type_id,
        string $resource_id
    ): JsonResponse
    {
        if ($this->viewAccessToResourceType((int) $resource_type_id) === false) {
            \App\Response\Responses::notFoundOrNotAccessible(trans('entities.resource'));
        }

        $item_type = Entity::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense' => $this->allocatedExpenseCollection((int) $resource_type_id, (int) $resource_id),
            'simple-expense' => $this->simpleExpenseCollection((int) $resource_type_id, (int) $resource_id),
            'simple-item' => $this->simpleItemCollection((int) $resource_type_id, (int) $resource_id),
            'game' => $this->gameCollection((int) $resource_type_id, (int) $resource_id),
            default => throw new \OutOfRangeException('No entity definition for ' . $item_type, 500),
        };
    }

    private function allocatedExpenseCollection(int $resource_type_id, int $resource_id): JsonResponse
    {
        $response = new \App\ItemType\AllocatedExpense\ApiResponse\Item(
            $resource_type_id,
            $resource_id,
            $this->writeAccessToResourceType($resource_type_id),
            $this->user_id
        );

        return $response->collectionResponse();
    }

    private function gameCollection(int $resource_type_id, int $resource_id): JsonResponse
    {
        $response = new \App\ItemType\Game\ApiResponse\Item(
            $resource_type_id,
            $resource_id,
            $this->writeAccessToResourceType($resource_type_id),
            $this->user_id
        );

        return $response->collectionResponse();
    }

    private function simpleExpenseCollection(int $resource_type_id, int $resource_id): JsonResponse
    {
        $response = new \App\ItemType\SimpleExpense\ApiResponse\Item(
            $resource_type_id,
            $resource_id,
            $this->writeAccessToResourceType($resource_type_id),
            $this->user_id
        );

        return $response->collectionResponse();
    }

    private function simpleItemCollection(int $resource_type_id, int $resource_id): JsonResponse
    {
        $response = new \App\ItemType\SimpleItem\ApiResponse\Item(
            $resource_type_id,
            $resource_id,
            $this->writeAccessToResourceType($resource_type_id),
            $this->user_id
        );

        return $response->collectionResponse();
    }

    public function show(
        $resource_type_id,
        $resource_id,
        $item_id
    ): JsonResponse
    {
        if ($this->viewAccessToResourceType((int) $resource_type_id) === false) {
            \App\Response\Responses::notFoundOrNotAccessible(trans('entities.resource'));
        }

        $item_type = Entity::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense' => $this->allocatedExpense((int) $resource_type_id, (int) $resource_id, (int) $item_id),
            'simple-expense' => $this->simpleExpense((int) $resource_type_id, (int) $resource_id, (int) $item_id),
            'simple-item' => $this->simpleItem((int) $resource_type_id, (int) $resource_id, (int) $item_id),
            'game' => $this->game((int) $resource_type_id, (int) $resource_id, (int) $item_id),
            default => throw new \OutOfRangeException('No entity definition for ' . $item_type, 500),
        };
    }

    private function allocatedExpense(int $resource_type_id, int $resource_id, int $item_id): JsonResponse
    {
        $response = new \App\ItemType\AllocatedExpense\ApiResponse\Item(
            $resource_type_id,
            $resource_id,
            $this->writeAccessToResourceType($resource_type_id),
            $this->user_id
        );

        return $response->showResponse($item_id);
    }

    private function game(int $resource_type_id, int $resource_id, int $item_id): JsonResponse
    {
        $response = new \App\ItemType\Game\ApiResponse\Item(
            $resource_type_id,
            $resource_id,
            $this->writeAccessToResourceType($resource_type_id),
            $this->user_id
        );

        return $response->showResponse($item_id);
    }

    private function simpleExpense(int $resource_type_id, int $resource_id, int $item_id): JsonResponse
    {
        $response = new \App\ItemType\SimpleExpense\ApiResponse\Item(
            $resource_type_id,
            $resource_id,
            $this->writeAccessToResourceType($resource_type_id),
            $this->user_id
        );

        return $response->showResponse($item_id);
    }

    private function simpleItem(int $resource_type_id, int $resource_id, int $item_id): JsonResponse
    {
        $response = new \App\ItemType\SimpleItem\ApiResponse\Item(
            $resource_type_id,
            $resource_id,
            $this->writeAccessToResourceType($resource_type_id),
            $this->user_id
        );

        return $response->showResponse($item_id);
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

        /*$entity = Entity::item($resource_type_id);

        $response = new ItemCollection($this->permissions((int) $resource_type_id));

        return $response
            ->setEntity($entity)
            ->setAllowedParameters(
                $entity->allowedValuesForItemCollection(
                    $resource_type_id,
                    $resource_id,
                    $this->viewable_resource_types
                )
            )
            ->setAllowedFields((new Currency())->allowedValues())
            ->create()
            ->response();*/

        $item_type = Entity::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense' => $this->optionsAllocatedExpenseCollection((int) $resource_type_id, (int) $resource_id),
            'game' => $this->optionsGameCollection((int) $resource_type_id, (int) $resource_id),
            'simple-expense' => $this->optionsSimpleExpenseCollection((int) $resource_type_id, (int) $resource_id),
            'simple-item' => $this->optionsSimpleItemCollection((int) $resource_type_id, (int) $resource_id),
            default => throw new \OutOfRangeException('No entity definition for ' . $item_type, 500),
        };
    }

    private function optionsAllocatedExpenseCollection(int $resource_type_id, int $resource_id): JsonResponse
    {
        $item = new \App\ItemType\AllocatedExpense\Item();
        $response = new \App\Option\Item\AllocatedExpenseCollection($this->permissions($resource_type_id));

        return $response->setDynamicAllowedParameters(
            $item->allowedValuesForItemCollection(
                $resource_type_id,
                $resource_id,
                $this->viewable_resource_types
            )
        )
        ->setDynamicAllowedFields((new Currency())->allowedValues())
        ->create()
        ->response();
    }

    private function optionsGameCollection(int $resource_type_id, int $resource_id): JsonResponse
    {
        $item = new \App\ItemType\Game\Item();
        $response = new \App\Option\Item\GameCollection($this->permissions($resource_type_id));

        return $response->setDynamicAllowedParameters(
            $item->allowedValuesForItemCollection(
                $resource_type_id,
                $resource_id,
                $this->viewable_resource_types
            )
        )
        ->create()
        ->response();
    }

    private function optionsSimpleExpenseCollection(int $resource_type_id, int $resource_id): JsonResponse
    {
        $item = new \App\ItemType\SimpleExpense\Item();
        $response = new \App\Option\Item\SimpleExpenseCollection($this->permissions($resource_type_id));

        return $response->setDynamicAllowedParameters(
            $item->allowedValuesForItemCollection(
                $resource_type_id,
                $resource_id,
                $this->viewable_resource_types
            )
        )
        ->setDynamicAllowedFields((new Currency())->allowedValues())
        ->create()
        ->response();
    }

    private function optionsSimpleItemCollection(int $resource_type_id, int $resource_id): JsonResponse
    {
        $item = new \App\ItemType\SimpleItem\Item();
        $response = new \App\Option\Item\SimpleItemCollection($this->permissions($resource_type_id));

        return $response->setDynamicAllowedParameters(
            $item->allowedValuesForItemCollection(
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
            ->setDynamicAllowedFields($entity->allowedValuesForItem((int) $resource_type_id))
            ->create()
            ->response();
    }
}
