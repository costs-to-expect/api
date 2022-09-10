<?php

namespace App\Http\Controllers;

use App\ItemType\Select;
use App\ItemType\AllocatedExpense\AllowedValue as AllocatedExpenseAllowedValue;
use App\ItemType\Budget\AllowedValue as BudgetAllowedValue;
use App\ItemType\Game\AllowedValue as GameAllowedValue;
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
    ): JsonResponse {
        if ($this->hasViewAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.resource'));
        }

        $item_type = Select::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense' => $this->allocatedExpenseCollection((int) $resource_type_id, (int) $resource_id),
            'budget' => $this->budgetCollection((int) $resource_type_id, (int) $resource_id),
            'game' => $this->gameCollection((int) $resource_type_id, (int) $resource_id),
            default => throw new \OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    private function allocatedExpenseCollection(int $resource_type_id, int $resource_id): JsonResponse
    {
        $response = new \App\ItemType\AllocatedExpense\HttpResponse\Item(
            $resource_type_id,
            $resource_id,
            $this->hasWriteAccessToResourceType($resource_type_id),
            $this->user_id
        );

        return $response->collectionResponse();
    }

    private function budgetCollection(int $resource_type_id, int $resource_id): JsonResponse
    {
        $response = new \App\ItemType\Budget\HttpResponse\Item(
            $resource_type_id,
            $resource_id,
            $this->hasWriteAccessToResourceType($resource_type_id),
            $this->user_id
        );

        return $response->collectionResponse();
    }

    private function gameCollection(int $resource_type_id, int $resource_id): JsonResponse
    {
        $response = new \App\ItemType\Game\HttpResponse\Item(
            $resource_type_id,
            $resource_id,
            $this->hasWriteAccessToResourceType($resource_type_id),
            $this->user_id
        );

        return $response->collectionResponse();
    }

    public function show(
        $resource_type_id,
        $resource_id,
        $item_id
    ): JsonResponse {
        if ($this->hasViewAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.resource'));
        }

        $item_type = Select::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense' => $this->allocatedExpense((int) $resource_type_id, (int) $resource_id, (int) $item_id),
            'budget' => $this->budget((int) $resource_type_id, (int) $resource_id, (int) $item_id),
            'game' => $this->game((int) $resource_type_id, (int) $resource_id, (int) $item_id),
            default => throw new \OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    private function allocatedExpense(int $resource_type_id, int $resource_id, int $item_id): JsonResponse
    {
        $response = new \App\ItemType\AllocatedExpense\HttpResponse\Item(
            $resource_type_id,
            $resource_id,
            $this->hasWriteAccessToResourceType($resource_type_id),
            $this->user_id
        );

        return $response->showResponse($item_id);
    }

    private function budget(int $resource_type_id, int $resource_id, int $item_id): JsonResponse
    {
        $response = new \App\ItemType\Budget\HttpResponse\Item(
            $resource_type_id,
            $resource_id,
            $this->hasWriteAccessToResourceType($resource_type_id),
            $this->user_id
        );

        return $response->showResponse($item_id);
    }

    private function game(int $resource_type_id, int $resource_id, int $item_id): JsonResponse
    {
        $response = new \App\ItemType\Game\HttpResponse\Item(
            $resource_type_id,
            $resource_id,
            $this->hasWriteAccessToResourceType($resource_type_id),
            $this->user_id
        );

        return $response->showResponse($item_id);
    }

    public function optionsIndex(
        string $resource_type_id,
        string $resource_id
    ): JsonResponse {
        if ($this->hasViewAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.resource'));
        }

        $item_type = Select::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense' => $this->optionsAllocatedExpenseCollection((int) $resource_type_id, (int) $resource_id),
            'budget' => $this->optionsBudgetCollection((int) $resource_type_id, (int) $resource_id),
            'game' => $this->optionsGameCollection((int) $resource_type_id, (int) $resource_id),
            default => throw new \OutOfRangeException('No options item type definition for ' . $item_type, 500),
        };
    }

    private function optionsAllocatedExpenseCollection(int $resource_type_id, int $resource_id): JsonResponse
    {
        $allowed_values = new AllocatedExpenseAllowedValue(
            $this->viewable_resource_types,
            $resource_type_id,
            $resource_id
        );

        return (new \App\HttpOptionResponse\Item\AllocatedExpenseCollection($this->permissions($resource_type_id)))
            ->setAllowedValuesForParameters($allowed_values->parameterAllowedValuesForCollection())
            ->setAllowedValuesForFields($allowed_values->fieldAllowedValuesForCollection())
            ->create()
            ->response();
    }

    private function optionsBudgetCollection(int $resource_type_id, int $resource_id): JsonResponse
    {
        $allowed_values = new BudgetAllowedValue(
            $this->viewable_resource_types,
            $resource_type_id,
            $resource_id
        );

        return (new \App\HttpOptionResponse\Item\BudgetCollection($this->permissions($resource_type_id)))
            ->setAllowedValuesForParameters($allowed_values->parameterAllowedValuesForCollection())
            ->setAllowedValuesForFields($allowed_values->fieldAllowedValuesForCollection())
            ->create()
            ->response();
    }

    private function optionsGameCollection(int $resource_type_id, int $resource_id): JsonResponse
    {
        $allowed_values = new GameAllowedValue(
            $this->viewable_resource_types,
            $resource_type_id,
            $resource_id
        );

        return (new \App\HttpOptionResponse\Item\GameCollection($this->permissions($resource_type_id)))
            ->setAllowedValuesForParameters($allowed_values->parameterAllowedValuesForCollection())
            ->create()
            ->response();
    }

    public function optionsShow(
        string $resource_type_id,
        string $resource_id,
        string $item_id
    ): JsonResponse {
        if ($this->hasViewAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.resource'));
        }

        $item_type = Select::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense' => $this->optionsAllocatedExpenseShow((int) $resource_type_id, (int) $resource_id, (int) $item_id),
            'budget' => $this->optionsBudgetShow((int) $resource_type_id, (int) $resource_id, (int) $item_id),
            'game' => $this->optionsGameShow((int) $resource_type_id, (int) $resource_id, (int) $item_id),
            default => throw new \OutOfRangeException('No entity definition for ' . $item_type, 500),
        };
    }

    private function optionsAllocatedExpenseShow(
        string $resource_type_id,
        string $resource_id,
        string $item_id
    ): JsonResponse {
        if ($this->hasViewAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.item'));
        }

        $item = (new \App\ItemType\AllocatedExpense\Models\Item())->single($resource_type_id, $resource_id, $item_id);

        if ($item === null) {
            return \App\HttpResponse\Response::notFound(trans('entities.item'));
        }

        $allowed_values = new AllocatedExpenseAllowedValue(
            $this->viewable_resource_types,
            $resource_type_id,
            $resource_id
        );

        return (new \App\HttpOptionResponse\Item\AllocatedExpense($this->permissions((int) $resource_type_id)))
            ->setAllowedValuesForFields($allowed_values->fieldAllowedValuesForShow())
            ->create()
            ->response();
    }

    private function optionsBudgetShow(
        string $resource_type_id,
        string $resource_id,
        string $item_id
    ): JsonResponse {
        if ($this->hasViewAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.item'));
        }

        $item = (new \App\ItemType\Budget\Models\Item())->single($resource_type_id, $resource_id, $item_id);

        if ($item === null) {
            return \App\HttpResponse\Response::notFound(trans('entities.item'));
        }

        return (new \App\HttpOptionResponse\Item\Budget($this->permissions((int) $resource_type_id)))
            ->create()
            ->response();
    }

    private function optionsGameShow(
        string $resource_type_id,
        string $resource_id,
        string $item_id
    ): JsonResponse {
        if ($this->hasViewAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.item'));
        }

        $item = (new \App\ItemType\Game\Models\Item())->single($resource_type_id, $resource_id, $item_id);

        if ($item === null) {
            return \App\HttpResponse\Response::notFound(trans('entities.item'));
        }

        $allowed_values = new GameAllowedValue(
            $this->viewable_resource_types,
            $resource_type_id,
            $resource_id,
            $item_id
        );

        return (new \App\HttpOptionResponse\Item\Game($this->permissions((int) $resource_type_id)))
            ->setAllowedValuesForFields($allowed_values->fieldAllowedValuesForShow())
            ->create()
            ->response();
    }
}
