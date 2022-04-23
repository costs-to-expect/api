<?php

namespace App\Http\Controllers;

use App\HttpRequest\Parameter\Request;
use App\HttpResponse\Responses;
use App\ItemType\Select;
use App\Models\AllowedValue\Currency;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

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
            \App\HttpResponse\Responses::notFoundOrNotAccessible(trans('entities.resource'));
        }

        $item_type = Select::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense' => $this->allocatedExpenseCollection((int) $resource_type_id, (int) $resource_id),
            'simple-expense' => $this->simpleExpenseCollection((int) $resource_type_id, (int) $resource_id),
            'simple-item' => $this->simpleItemCollection((int) $resource_type_id, (int) $resource_id),
            'game' => $this->gameCollection((int) $resource_type_id, (int) $resource_id),
            default => throw new \OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    private function allocatedExpenseCollection(int $resource_type_id, int $resource_id): JsonResponse
    {
        $response = new \App\ItemType\AllocatedExpense\HttpResponse\Item(
            $resource_type_id,
            $resource_id,
            $this->writeAccessToResourceType($resource_type_id),
            $this->user_id
        );

        return $response->collectionResponse();
    }

    private function gameCollection(int $resource_type_id, int $resource_id): JsonResponse
    {
        $response = new \App\ItemType\Game\HttpResponse\Item(
            $resource_type_id,
            $resource_id,
            $this->writeAccessToResourceType($resource_type_id),
            $this->user_id
        );

        return $response->collectionResponse();
    }

    private function simpleExpenseCollection(int $resource_type_id, int $resource_id): JsonResponse
    {
        $response = new \App\ItemType\SimpleExpense\HttpResponse\Item(
            $resource_type_id,
            $resource_id,
            $this->writeAccessToResourceType($resource_type_id),
            $this->user_id
        );

        return $response->collectionResponse();
    }

    private function simpleItemCollection(int $resource_type_id, int $resource_id): JsonResponse
    {
        $response = new \App\ItemType\SimpleItem\HttpResponse\Item(
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
            \App\HttpResponse\Responses::notFoundOrNotAccessible(trans('entities.resource'));
        }

        $item_type = Select::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense' => $this->allocatedExpense((int) $resource_type_id, (int) $resource_id, (int) $item_id),
            'simple-expense' => $this->simpleExpense((int) $resource_type_id, (int) $resource_id, (int) $item_id),
            'simple-item' => $this->simpleItem((int) $resource_type_id, (int) $resource_id, (int) $item_id),
            'game' => $this->game((int) $resource_type_id, (int) $resource_id, (int) $item_id),
            default => throw new \OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    private function allocatedExpense(int $resource_type_id, int $resource_id, int $item_id): JsonResponse
    {
        $response = new \App\ItemType\AllocatedExpense\HttpResponse\Item(
            $resource_type_id,
            $resource_id,
            $this->writeAccessToResourceType($resource_type_id),
            $this->user_id
        );

        return $response->showResponse($item_id);
    }

    private function game(int $resource_type_id, int $resource_id, int $item_id): JsonResponse
    {
        $response = new \App\ItemType\Game\HttpResponse\Item(
            $resource_type_id,
            $resource_id,
            $this->writeAccessToResourceType($resource_type_id),
            $this->user_id
        );

        return $response->showResponse($item_id);
    }

    private function simpleExpense(int $resource_type_id, int $resource_id, int $item_id): JsonResponse
    {
        $response = new \App\ItemType\SimpleExpense\HttpResponse\Item(
            $resource_type_id,
            $resource_id,
            $this->writeAccessToResourceType($resource_type_id),
            $this->user_id
        );

        return $response->showResponse($item_id);
    }

    private function simpleItem(int $resource_type_id, int $resource_id, int $item_id): JsonResponse
    {
        $response = new \App\ItemType\SimpleItem\HttpResponse\Item(
            $resource_type_id,
            $resource_id,
            $this->writeAccessToResourceType($resource_type_id),
            $this->user_id
        );

        return $response->showResponse($item_id);
    }

    public function optionsIndex(
        string $resource_type_id,
        string $resource_id
    ): JsonResponse
    {
        if ($this->viewAccessToResourceType((int) $resource_type_id) === false) {
            \App\HttpResponse\Responses::notFoundOrNotAccessible(trans('entities.resource'));
        }

        $item_type = Select::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense' => $this->optionsAllocatedExpenseCollection((int) $resource_type_id, (int) $resource_id),
            'game' => $this->optionsGameCollection((int) $resource_type_id, (int) $resource_id),
            'simple-expense' => $this->optionsSimpleExpenseCollection((int) $resource_type_id, (int) $resource_id),
            'simple-item' => $this->optionsSimpleItemCollection((int) $resource_type_id, (int) $resource_id),
            default => throw new \OutOfRangeException('No options item type definition for ' . $item_type, 500),
        };
    }

    private function optionsAllocatedExpenseCollection(int $resource_type_id, int $resource_id): JsonResponse
    {
        $item = new \App\ItemType\AllocatedExpense\Item();
        $response = new \App\HttpOptionResponse\Item\AllocatedExpenseCollection($this->permissions($resource_type_id));

        return $response->setAllowedValuesForParameters(
            $item->allowedValuesForItemCollection(
                $resource_type_id,
                $resource_id,
                $this->viewable_resource_types
            )
        )
        ->setAllowedValuesForFields((new Currency())->allowedValues())
        ->create()
        ->response();
    }

    private function optionsGameCollection(int $resource_type_id, int $resource_id): JsonResponse
    {
        $item = new \App\ItemType\Game\Item();
        $response = new \App\HttpOptionResponse\Item\GameCollection($this->permissions($resource_type_id));

        return $response->setAllowedValuesForParameters(
                $item->allowedValuesForItemCollection(
                    $resource_type_id,
                    $resource_id,
                    $this->viewable_resource_types
                )
            )
            ->create()
            ->response();
    }

    private function allowedValuesForSimpleExpenseCollectionParameters(int $resource_type_id, int $resource_id): array
    {
        $parameters = Config::get('api.item-type-simple-expense.parameters', []);
        $parameters_set_in_request = Request::fetch(
            array_keys($parameters),
            $resource_type_id,
            $resource_id
        );

        return [
            'category' => ['allowed_values' => $this->assignAllowedValuesForCategory($resource_type_id, $this->viewable_resource_types)],
            'subcategory' => ['allowed_values' => $this->assignAllowedValuesForSubcategory($resource_type_id, $parameters_set_in_request)]
        ];
    }

    private function allowedValuesForSimpleExpenseCollectionFields(): array
    {
        return [
            'currency_id' => ['allowed_values' => $this->assignAllowedValuesForCurrency()]
        ];
    }

    private function assignAllowedValuesForCategory(int $resource_type_id, array $viewable_resource_types): array
    {
        $allowed_values = [];

        $categories = (new Category())->paginatedCollection(
            $resource_type_id,
            $viewable_resource_types,
            0,
            100
        );

        foreach ($categories as $category) {
            $category_id = $this->hash->encode('category', $category['category_id']);

            if ($category_id === false) {
                Responses::unableToDecode();
            }

            $allowed_values[$category_id] = [
                'uri' => route('category.show', ['resource_type_id' => $resource_type_id, 'category_id' => $category_id], false),
                'value' => $category_id,
                'name' => $category['category_name'],
                'description' => trans('item-type-simple-expense/allowed-values.description-prefix-category') .
                    $category['category_name'] .
                    trans('item-type-simple-expense/allowed-values.description-suffix-category')
            ];
        }

        return $allowed_values;
    }

    private function assignAllowedValuesForCurrency(): array
    {
        $allowed_values = [];

        $currencies = (new \App\Models\Currency())->minimisedCollection();

        foreach ($currencies as $currency) {
            $id = $this->hash->encode('currency', $currency['currency_id']);

            if ($id === false) {
                Responses::unableToDecode();
            }

            $allowed_values[$id] = [
                'uri' => route('currency.show', ['currency_id' => $id], false),
                'value' => $id,
                'name' => $currency['currency_name'],
                'description' => $currency['currency_name']
            ];
        }

        return $allowed_values;
    }

    private function assignAllowedValuesForSubcategory(
        int $resource_type_id,
        array $parameters_set_in_request
    ): array
    {
        $allowed_values = [];

        if (
            array_key_exists('category', $parameters_set_in_request) === true &&
            $parameters_set_in_request['category'] !== null
        ) {

            $subcategories = (new Subcategory())->paginatedCollection(
                $resource_type_id,
                (int) $parameters_set_in_request['category']
            );

            $category_id = $this->hash->encode('category', $parameters_set_in_request['category']);

            foreach ($subcategories as $subcategory) {
                $subcategory_id = $this->hash->encode('subcategory', $subcategory['subcategory_id']);

                $allowed_values[$subcategory_id] = [
                    'uri' => route('subcategory.show', ['resource_type_id' => $resource_type_id, 'category_id' => $category_id, 'subcategory_id' => $subcategory_id], false),
                    'value' => $subcategory_id,
                    'name' => $subcategory['subcategory_name'],
                    'description' => trans('item-type-simple-expense/allowed-values.description-prefix-subcategory') .
                        $subcategory['subcategory_name'] . trans('item-type-simple-expense/allowed-values.description-suffix-subcategory')
                ];
            }
        }

        return $allowed_values;
    }

    private function optionsSimpleExpenseCollection(int $resource_type_id, int $resource_id): JsonResponse
    {
        return (new \App\HttpOptionResponse\Item\SimpleExpenseCollection($this->permissions($resource_type_id)))
            ->setAllowedValuesForParameters(
                $this->allowedValuesForSimpleExpenseCollectionParameters(
                    $resource_type_id,
                    $resource_id
                )
            )
            ->setAllowedValuesForFields(
                $this->allowedValuesForSimpleExpenseCollectionFields()
            )
            ->create()
            ->response();
    }

    private function optionsSimpleItemCollection(int $resource_type_id, int $resource_id): JsonResponse
    {
        $item = new \App\ItemType\SimpleItem\Item();
        $response = new \App\HttpOptionResponse\Item\SimpleItemCollection($this->permissions($resource_type_id));

        return $response->setAllowedValuesForParameters(
            $item->allowedValuesForItemCollection(
                $resource_type_id,
                $resource_id,
                $this->viewable_resource_types
            )
        )
        ->create()
        ->response();
    }

    public function optionsShow(
        string $resource_type_id,
        string $resource_id,
        string $item_id
    ): JsonResponse
    {
        if ($this->viewAccessToResourceType((int) $resource_type_id) === false) {
            \App\HttpResponse\Responses::notFoundOrNotAccessible(trans('entities.resource'));
        }

        $item_type = Select::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense' => $this->optionsAllocatedExpenseShow((int) $resource_type_id, (int) $resource_id, (int) $item_id),
            'game' => $this->optionsGameShow((int) $resource_type_id, (int) $resource_id, (int) $item_id),
            'simple-expense' => $this->optionsSimpleExpenseShow((int) $resource_type_id, (int) $resource_id, (int) $item_id),
            'simple-item' => $this->optionsSimpleItemShow((int) $resource_type_id, (int) $resource_id, (int) $item_id),
            default => throw new \OutOfRangeException('No entity definition for ' . $item_type, 500),
        };
    }

    private function optionsAllocatedExpenseShow(
        string $resource_type_id,
        string $resource_id,
        string $item_id
    ): JsonResponse
    {
        if ($this->viewAccessToResourceType((int) $resource_type_id) === false) {
            \App\HttpResponse\Responses::notFoundOrNotAccessible(trans('entities.item'));
        }

        $item = new \App\ItemType\AllocatedExpense\Item();
        $model = new \App\ItemType\AllocatedExpense\Models\Item();
        $item_data = $model->single($resource_type_id, $resource_id, $item_id);

        if ($item_data === null) {
            return \App\HttpResponse\Responses::notFound(trans('entities.item'));
        }

        $response = new \App\HttpOptionResponse\Item\AllocatedExpense($this->permissions((int) $resource_type_id));

        return $response->setAllowedValuesForFields(
                $item->allowedValuesForItem((int) $resource_type_id)
            )
            ->create()
            ->response();
    }

    private function optionsGameShow(
        string $resource_type_id,
        string $resource_id,
        string $item_id
    ): JsonResponse
    {
        if ($this->viewAccessToResourceType((int) $resource_type_id) === false) {
            \App\HttpResponse\Responses::notFoundOrNotAccessible(trans('entities.item'));
        }

        $item = new \App\ItemType\Game\Item();
        $model = new \App\ItemType\Game\Models\Item();
        $item_data = $model->single($resource_type_id, $resource_id, $item_id);

        if ($item_data === null) {
            return \App\HttpResponse\Responses::notFound(trans('entities.item'));
        }

        $response = new \App\HttpOptionResponse\Item\Game($this->permissions((int) $resource_type_id));

        return $response->setAllowedValuesForFields(
                $item->allowedValuesForItem((int) $resource_type_id)
            )
            ->create()
            ->response();
    }

    private function optionsSimpleExpenseShow(
        string $resource_type_id,
        string $resource_id,
        string $item_id
    ): JsonResponse
    {
        if ($this->viewAccessToResourceType((int) $resource_type_id) === false) {
            \App\HttpResponse\Responses::notFoundOrNotAccessible(trans('entities.item'));
        }

        $item = new \App\ItemType\SimpleExpense\Item();
        $model = new \App\ItemType\SimpleExpense\Models\Item();
        $item_data = $model->single($resource_type_id, $resource_id, $item_id);

        if ($item_data === null) {
            return \App\HttpResponse\Responses::notFound(trans('entities.item'));
        }

        $response = new \App\HttpOptionResponse\Item\SimpleExpense($this->permissions((int) $resource_type_id));

        return $response->setAllowedValuesForFields(
                $item->allowedValuesForItem((int) $resource_type_id)
            )
            ->create()
            ->response();
    }

    private function optionsSimpleItemShow(
        string $resource_type_id,
        string $resource_id,
        string $item_id
    ): JsonResponse
    {
        if ($this->viewAccessToResourceType((int) $resource_type_id) === false) {
            \App\HttpResponse\Responses::notFoundOrNotAccessible(trans('entities.item'));
        }

        $item = new \App\ItemType\SimpleItem\Item();
        $model = new \App\ItemType\SimpleItem\Models\Item();
        $item_data = $model->single($resource_type_id, $resource_id, $item_id);

        if ($item_data === null) {
            return \App\HttpResponse\Responses::notFound(trans('entities.item'));
        }

        $response = new \App\HttpOptionResponse\Item\SimpleItem($this->permissions((int) $resource_type_id));

        return $response->setAllowedValuesForFields(
                $item->allowedValuesForItem((int) $resource_type_id)
            )
            ->create()
            ->response();
    }
}
