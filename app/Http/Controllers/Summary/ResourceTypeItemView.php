<?php

namespace App\Http\Controllers\Summary;

use App\Http\Controllers\Controller;
use App\ItemType\Entity;
use App\Option\ResourceTypeItem\Summary\AllocatedExpense;
use App\Option\ResourceTypeItem\Summary\Game;
use App\Option\ResourceTypeItem\Summary\SimpleExpense;
use App\Option\ResourceTypeItem\Summary\SimpleItem;
use App\Response\Responses;
use Illuminate\Http\JsonResponse;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceTypeItemView extends Controller
{
    public function index(string $resource_type_id): JsonResponse
    {
        if ($this->viewAccessToResourceType((int)$resource_type_id) === false) {
            Responses::notFoundOrNotAccessible(trans('entities.resource'));
        }

        $item_type = Entity::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense' => $this->allocatedExpenseSummary((int) $resource_type_id),
            'simple-expense' => $this->simpleExpenseSummary((int) $resource_type_id),
            'simple-item' => $this->simpleItemSummary((int) $resource_type_id),
            'game' => $this->gameSummary((int) $resource_type_id),
            default => throw new \OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    private function allocatedExpenseSummary(int $resource_type_id): JsonResponse
    {
        $response = new \App\ItemType\AllocatedExpense\ApiResponse\SummaryResourceTypeItem(
            $resource_type_id,
            $this->writeAccessToResourceType($resource_type_id),
            $this->user_id
        );

        return $response->response();
    }

    private function gameSummary(int $resource_type_id): JsonResponse
    {
        $response = new \App\ItemType\Game\ApiResponse\SummaryResourceTypeItem(
            $resource_type_id,
            $this->writeAccessToResourceType($resource_type_id),
            $this->user_id
        );

        return $response->response();
    }

    private function simpleExpenseSummary(int $resource_type_id): JsonResponse
    {
        $response = new \App\ItemType\SimpleExpense\ApiResponse\SummaryResourceTypeItem(
            $resource_type_id,
            $this->writeAccessToResourceType($resource_type_id),
            $this->user_id
        );

        return $response->response();
    }

    private function simpleItemSummary(int $resource_type_id): JsonResponse
    {
        $response = new \App\ItemType\SimpleItem\ApiResponse\SummaryResourceTypeItem(
            $resource_type_id,
            $this->writeAccessToResourceType($resource_type_id),
            $this->user_id
        );

        return $response->response();
    }

    public function optionsIndex(string $resource_type_id): JsonResponse
    {
        if ($this->viewAccessToResourceType((int) $resource_type_id) === false) {
            Responses::notFoundOrNotAccessible(trans('entities.resource'));
        }

        $item_type = Entity::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense' => $this->optionsAllocatedExpense((int) $resource_type_id),
            'game' => $this->optionsGame((int) $resource_type_id),
            'simple-expense' => $this->optionsSimpleExpense((int) $resource_type_id),
            'simple-item' => $this->optionsSimpleItem((int) $resource_type_id),
            default => throw new \OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    private function optionsAllocatedExpense(int $resource_type_id): JsonResponse
    {
        $item = new \App\ItemType\AllocatedExpense\Item();
        $allowed_values = $item->allowedValuesForResourceTypeItemCollection(
            $resource_type_id,
            $this->viewable_resource_types
        );

        $response = new AllocatedExpense($this->permissions($resource_type_id));

        return $response->setDynamicAllowedParameters($allowed_values)
            ->create()
            ->response();
    }

    private function optionsGame(int $resource_type_id): JsonResponse
    {
        $item = new \App\ItemType\Game\Item();
        $allowed_values = $item->allowedValuesForResourceTypeItemCollection(
            $resource_type_id,
            $this->viewable_resource_types
        );

        $response = new Game($this->permissions($resource_type_id));

        return $response->setDynamicAllowedParameters($allowed_values)
            ->create()
            ->response();
    }

    private function optionsSimpleExpense(int $resource_type_id): JsonResponse
    {
        $item = new \App\ItemType\SimpleExpense\Item();
        $allowed_values = $item->allowedValuesForResourceTypeItemCollection(
            $resource_type_id,
            $this->viewable_resource_types
        );

        $response = new SimpleExpense($this->permissions($resource_type_id));

        return $response->setDynamicAllowedParameters($allowed_values)
            ->create()
            ->response();
    }

    private function optionsSimpleItem(int $resource_type_id): JsonResponse
    {
        $item = new \App\ItemType\SimpleItem\Item();
        $allowed_values = $item->allowedValuesForResourceTypeItemCollection(
            $resource_type_id,
            $this->viewable_resource_types
        );

        $response = new SimpleItem($this->permissions($resource_type_id));

        return $response->setDynamicAllowedParameters($allowed_values)
            ->create()
            ->response();
    }
}
