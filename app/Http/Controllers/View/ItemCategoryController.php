<?php

namespace App\Http\Controllers\View;

use App\Http\Controllers\Controller;
use App\HttpOptionResponse\ItemCategory\AllocatedExpense;
use App\HttpOptionResponse\ItemCategory\AllocatedExpenseCollection;
use App\HttpOptionResponse\ItemCategory\Game;
use App\HttpOptionResponse\ItemCategory\GameCollection;
use App\HttpResponse\Header;
use App\ItemType\Select;
use App\Models\ItemCategory;
use App\Transformer\ItemCategory as ItemCategoryTransformer;
use Illuminate\Http\JsonResponse;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2023
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemCategoryController extends Controller
{
    public function index(string $resource_type_id, string $resource_id, string $item_id): JsonResponse
    {
        if ($this->hasViewAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.item'));
        }

        $item_type = Select::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense', 'game' => $this->itemCategoryCollection((int) $resource_type_id, (int) $resource_id, (int) $item_id),
            default => throw new \OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    private function itemCategoryCollection(int $resource_type_id, int $resource_id, int $item_id): JsonResponse
    {
        $cache_control = new \App\Cache\Control($this->user_id);
        $cache_control->setTtlOneWeek();

        $cache_collection = new \App\Cache\Response\Collection();
        $cache_collection->setFromCache($cache_control->getByKey(request()->getRequestUri()));

        if ($cache_control->isRequestCacheable() === false || $cache_collection->valid() === false) {
            $item_category = (new ItemCategory())->paginatedCollection(
                $resource_type_id,
                $resource_id,
                $item_id
            );

            if ((count($item_category) === 0)) {
                $collection = [];
            } else {
                $collection = array_map(
                    static function ($category) {
                        return (new ItemCategoryTransformer($category))->asArray();
                    },
                    $item_category
                );
            }

            $headers = new Header();
            $headers->add('X-Total-Count', count($collection));
            $headers->add('X-Count', count($collection));
            $headers->addCacheControl($cache_control->visibility(), $cache_control->ttl());

            $cache_collection->create(count($collection), $collection, [], $headers->headers());
            $cache_control->putByKey(request()->getRequestUri(), $cache_collection->content());
        }

        return response()->json($cache_collection->collection(), 200, $cache_collection->headers());
    }

    public function show(
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $item_category_id = null
    ): JsonResponse {
        if ($this->hasViewAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.item'));
        }

        if ($item_category_id === null) {
            return \App\HttpResponse\Response::notFound(trans('entities.item-category'));
        }

        $item_type = Select::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense', 'game' => $this->itemCategory((int) $resource_type_id, (int) $resource_id, (int) $item_id, (int) $item_category_id),
            default => throw new \OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    private function itemCategory(
        int $resource_type_id,
        int $resource_id,
        int $item_id,
        int $item_category_id = null
    ): JsonResponse {
        $item_category = (new ItemCategory())->single(
            $resource_type_id,
            $resource_id,
            $item_id,
            $item_category_id
        );

        if ($item_category === null) {
            return \App\HttpResponse\Response::notFound(trans('entities.item-category'));
        }

        $headers = new Header();
        $headers->item();

        return response()->json(
            (new ItemCategoryTransformer($item_category))->asArray(),
            200,
            $headers->headers()
        );
    }

    public function optionsIndex(string $resource_type_id, string $resource_id, string $item_id): JsonResponse
    {
        if ($this->hasViewAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.item'));
        }

        $item_type = Select::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense' => $this->optionsAllocatedExpenseCollection((int) $resource_type_id),
            'game' => $this->optionsGameCollection((int) $resource_type_id),
            default => throw new \OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    private function optionsAllocatedExpenseCollection(int $resource_type_id): JsonResponse
    {
        if ($this->hasViewAccessToResourceType($resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.item'));
        }

        $response = new AllocatedExpenseCollection($this->permissions((int) $resource_type_id));

        return $response
            ->setAllowedValuesForFields(
                (new \App\Models\AllowedValue\Category())->allowedValues($resource_type_id)
            )
            ->create()
            ->response();
    }

    private function optionsGameCollection(int $resource_type_id): JsonResponse
    {
        if ($this->hasViewAccessToResourceType($resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.item'));
        }

        $response = new GameCollection($this->permissions($resource_type_id));

        return $response
            ->setAllowedValuesForFields(
                (new \App\Models\AllowedValue\Category())->allowedValues($resource_type_id)
            )
            ->create()
            ->response();
    }

    public function optionsShow(
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $item_category_id = null
    ): JsonResponse {
        if ($this->hasViewAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.item'));
        }

        if ($item_category_id === null) {
            return \App\HttpResponse\Response::notFound(trans('entities.item-category'));
        }

        $item_type = Select::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense' => $this->optionsAllocatedExpenseShow((int) $resource_type_id, (int) $resource_id, (int) $item_id, (int) $item_category_id),
            'game' => $this->optionsGameShow((int) $resource_type_id, (int) $resource_id, (int) $item_id, (int) $item_category_id),
            default => throw new \OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    private function optionsAllocatedExpenseShow(
        int $resource_type_id,
        int $resource_id,
        int $item_id,
        int $item_category_id
    ): JsonResponse {
        $item_category = (new ItemCategory())->single(
            $resource_type_id,
            $resource_id,
            $item_id,
            $item_category_id
        );

        if ($item_category === null) {
            return \App\HttpResponse\Response::notFound(trans('entities.item-category'));
        }

        $response = new AllocatedExpense($this->permissions((int) $resource_type_id));

        return $response->create()->response();
    }

    private function optionsGameShow(
        int $resource_type_id,
        int $resource_id,
        int $item_id,
        int $item_category_id
    ): JsonResponse {
        $item_category = (new ItemCategory())->single(
            $resource_type_id,
            $resource_id,
            $item_id,
            $item_category_id
        );

        if ($item_category === null) {
            return \App\HttpResponse\Response::notFound(trans('entities.item-category'));
        }

        $response = new Game($this->permissions((int) $resource_type_id));

        return $response->create()->response();
    }
}
