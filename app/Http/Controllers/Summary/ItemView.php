<?php

namespace App\Http\Controllers\Summary;

use App\Http\Controllers\Controller;
use App\HttpResponse\Responses;
use App\ItemType\AllocatedExpense\AllowedValue as AllocatedExpenseAllowedValue;
use App\ItemType\Game\AllowedValue as GameAllowedValue;
use App\ItemType\SimpleExpense\AllowedValue as SimpleExpenseAllowedValue;
use App\ItemType\SimpleItem\AllowedValue as SimpleItemAllowedValue;
use App\ItemType\Select;
use App\HttpOptionResponse\Item\Summary\AllocatedExpense;
use App\HttpOptionResponse\Item\Summary\Game;
use App\HttpOptionResponse\Item\Summary\SimpleExpense;
use App\HttpOptionResponse\Item\Summary\SimpleItem;
use Illuminate\Http\JsonResponse;

class ItemView extends Controller
{
    public function index(string $resource_type_id, string $resource_id): JsonResponse
    {
        if ($this->viewAccessToResourceType((int)$resource_type_id) === false) {
            Responses::notFoundOrNotAccessible(trans('entities.resource'));
        }

        $item_type = Select::itemType((int) $resource_type_id);

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
        $response = new \App\ItemType\AllocatedExpense\HttpResponse\Summary(
            $resource_type_id,
            $resource_id,
            $this->writeAccessToResourceType($resource_type_id),
            $this->user_id
        );

        return $response->response();
    }

    private function gameSummary(int $resource_type_id, int $resource_id): JsonResponse
    {
        $response = new \App\ItemType\Game\HttpResponse\Summary(
            $resource_type_id,
            $resource_id,
            $this->writeAccessToResourceType($resource_type_id),
            $this->user_id
        );

        return $response->response();
    }

    private function simpleExpenseSummary(int $resource_type_id, int $resource_id): JsonResponse
    {
        $response = new \App\ItemType\SimpleExpense\HttpResponse\Summary(
            $resource_type_id,
            $resource_id,
            $this->writeAccessToResourceType($resource_type_id),
            $this->user_id
        );

        return $response->response();
    }

    private function simpleItemSummary(int $resource_type_id, int $resource_id): JsonResponse
    {
        $response = new \App\ItemType\SimpleItem\HttpResponse\Summary(
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

        $item_type = Select::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense' => $this->optionsAllocatedExpense((int) $resource_type_id, (int) $resource_id),
            'simple-expense' => $this->optionsSimpleExpense((int) $resource_type_id, (int) $resource_id),
            'simple-item' => $this->optionsSimpleItem((int) $resource_type_id, (int) $resource_id),
            'game' => $this->optionsGame((int) $resource_type_id, (int) $resource_id),
            default => throw new \OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    private function optionsAllocatedExpense(int $resource_type_id, int $resource_id): JsonResponse
    {
        $allowed_values = new AllocatedExpenseAllowedValue(
            $this->viewable_resource_types,
            $resource_type_id,
            $resource_id
        );

        return (new AllocatedExpense($this->permissions($resource_type_id)))
            ->setAllowedValuesForParameters($allowed_values->parameterAllowedValuesForCollection())
            ->create()
            ->response();
    }

    private function optionsGame(int $resource_type_id, int $resource_id): JsonResponse
    {
        $allowed_values = new GameAllowedValue(
            $this->viewable_resource_types,
            $resource_type_id,
            $resource_id
        );

        return (new Game($this->permissions($resource_type_id)))
            ->setAllowedValuesForParameters($allowed_values->parameterAllowedValuesForCollection())
            ->create()
            ->response();
    }

    private function optionsSimpleExpense(int $resource_type_id, int $resource_id): JsonResponse
    {
        $allowed_values = new SimpleExpenseAllowedValue(
            $this->viewable_resource_types,
            $resource_type_id,
            $resource_id
        );

        return (new SimpleExpense($this->permissions($resource_type_id)))
            ->setAllowedValuesForParameters($allowed_values->parameterAllowedValuesForCollection())
            ->create()
            ->response();
    }

    private function optionsSimpleItem(int $resource_type_id, int $resource_id): JsonResponse
    {
        $allowed_values = new SimpleItemAllowedValue(
            $this->viewable_resource_types,
            $resource_type_id,
            $resource_id
        );

        return (new SimpleItem($this->permissions($resource_type_id)))
            ->setAllowedValuesForParameters($allowed_values->parameterAllowedValuesForCollection())
            ->create()
            ->response();
    }
}
