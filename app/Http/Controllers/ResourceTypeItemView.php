<?php

namespace App\Http\Controllers;

use App\ItemType\Select;
use App\ItemType\AllocatedExpense\AllowedValue as AllocatedExpenseAllowedValue;
use App\ItemType\Game\AllowedValue as GameAllowedValue;
use App\ItemType\SimpleExpense\AllowedValue as SimpleExpenseAllowedValue;
use App\ItemType\SimpleItem\AllowedValue as SimpleItemAllowedValue;
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
        if ($this->hasViewAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Responses::notFoundOrNotAccessible(trans('entities.resource-type'));
        }

        $item_type = Select::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense' => $this->allocatedExpenseCollection((int) $resource_type_id),
            'game' => $this->gameCollection((int) $resource_type_id),
            'simple-expense' => $this->simpleExpenseCollection((int) $resource_type_id),
            'simple-item' => $this->simpleItemCollection((int) $resource_type_id),
            default => throw new \OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    private function allocatedExpenseCollection(int $resource_type_id): JsonResponse
    {
        $response = new \App\ItemType\AllocatedExpense\HttpResponse\ResourceTypeItem(
            $resource_type_id,
            $this->hasWriteAccessToResourceType($resource_type_id),
            $this->user_id
        );

        return $response->response();
    }

    private function gameCollection(int $resource_type_id): JsonResponse
    {
        $response = new \App\ItemType\Game\HttpResponse\ResourceTypeItem(
            $resource_type_id,
            $this->hasWriteAccessToResourceType($resource_type_id),
            $this->user_id
        );

        return $response->response();
    }

    private function simpleExpenseCollection(int $resource_type_id): JsonResponse
    {
        $response = new \App\ItemType\SimpleExpense\HttpResponse\ResourceTypeItem(
            $resource_type_id,
            $this->hasWriteAccessToResourceType($resource_type_id),
            $this->user_id
        );

        return $response->response();
    }

    private function simpleItemCollection(int $resource_type_id): JsonResponse
    {
        $response = new \App\ItemType\SimpleItem\HttpResponse\ResourceTypeItem(
            $resource_type_id,
            $this->hasWriteAccessToResourceType($resource_type_id),
            $this->user_id
        );

        return $response->response();
    }

    public function optionsIndex(string $resource_type_id): JsonResponse
    {
        if ($this->hasViewAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Responses::notFoundOrNotAccessible(trans('entities.resource-type'));
        }

        $item_type = Select::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense' => $this->optionsAllocatedExpenseCollection((int) $resource_type_id),
            'game' => $this->optionsGameCollection((int) $resource_type_id),
            'simple-expense' => $this->optionsSimpleExpenseCollection((int) $resource_type_id),
            'simple-item' => $this->optionsSimpleItemCollection((int) $resource_type_id),
            default => throw new \OutOfRangeException('No options item type definition for ' . $item_type, 500),
        };
    }

    private function optionsAllocatedExpenseCollection(int $resource_type_id): JsonResponse
    {
        $allowed_values = new AllocatedExpenseAllowedValue(
            $this->viewable_resource_types,
            $resource_type_id
        );

        return (new \App\HttpOptionResponse\ResourceTypeItem\AllocatedExpenseCollection($this->permissions($resource_type_id)))
            ->setAllowedValuesForParameters($allowed_values->parameterAllowedValuesForResourceTypeCollection())
            ->create()
            ->response();
    }

    private function optionsGameCollection(int $resource_type_id): JsonResponse
    {
        $allowed_values = new GameAllowedValue(
            $this->viewable_resource_types,
            $resource_type_id
        );

        return (new \App\HttpOptionResponse\ResourceTypeItem\GameCollection($this->permissions($resource_type_id)))
            ->setAllowedValuesForParameters($allowed_values->parameterAllowedValuesForResourceTypeCollection())
            ->create()
            ->response();
    }

    private function optionsSimpleExpenseCollection(int $resource_type_id): JsonResponse
    {
        $allowed_values = new SimpleExpenseAllowedValue(
            $this->viewable_resource_types,
            $resource_type_id
        );

        return (new \App\HttpOptionResponse\ResourceTypeItem\SimpleExpenseCollection($this->permissions($resource_type_id)))
            ->setAllowedValuesForParameters($allowed_values->parameterAllowedValuesForResourceTypeCollection())
            ->create()
            ->response();
    }

    private function optionsSimpleItemCollection(int $resource_type_id): JsonResponse
    {
        $allowed_values = new SimpleItemAllowedValue(
            $this->viewable_resource_types,
            $resource_type_id
        );

        return (new \App\HttpOptionResponse\ResourceTypeItem\SimpleItemCollection($this->permissions($resource_type_id)))
            ->setAllowedValuesForParameters($allowed_values->parameterAllowedValuesForResourceTypeCollection())
            ->create()
            ->response();
    }
}
