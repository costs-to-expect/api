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

        $item_type = Entity::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense' => $this->allocatedExpenseSummary((int) $resource_type_id, (int) $resource_id),
            'simple-expense' => $this->simpleExpenseSummary((int) $resource_type_id, (int) $resource_id),
            'simple-item' => $this->simpleItemSummary((int) $resource_type_id, (int) $resource_id),
            'game' => $this->gameSummary((int) $resource_type_id, (int) $resource_id),
            default => throw new \OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    private function allocatedExpenseSummary(int $resource_type_id, int $resource_id): JsonResponse
    {
        $response = new \App\ItemType\AllocatedExpense\ApiResponse\Summary(
            $resource_type_id,
            $resource_id,
            $this->writeAccessToResourceType($resource_type_id),
            $this->user_id
        );

        return $response->response();
    }

    private function gameSummary(int $resource_type_id, int $resource_id): JsonResponse
    {
        $response = new \App\ItemType\Game\ApiResponse\Summary(
            $resource_type_id,
            $resource_id,
            $this->writeAccessToResourceType($resource_type_id),
            $this->user_id
        );

        return $response->response();
    }

    private function simpleExpenseSummary(int $resource_type_id, int $resource_id): JsonResponse
    {
        $response = new \App\ItemType\SimpleExpense\ApiResponse\Summary(
            $resource_type_id,
            $resource_id,
            $this->writeAccessToResourceType($resource_type_id),
            $this->user_id
        );

        return $response->response();
    }

    private function simpleItemSummary(int $resource_type_id, int $resource_id): JsonResponse
    {
        $response = new \App\ItemType\SimpleItem\ApiResponse\Summary(
            $resource_type_id,
            $resource_id,
            $this->writeAccessToResourceType($resource_type_id),
            $this->user_id
        );

        return $response->response();
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
            ->setDynamicAllowedParameters($allowed_values)
            ->create()
            ->response();
    }
}
