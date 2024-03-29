<?php

namespace App\Http\Controllers\View;

use App\Http\Controllers\Controller;
use App\HttpOptionResponse\ItemSubcategory\AllocatedExpenseCollection;
use App\HttpOptionResponse\ItemSubcategoryItem;
use App\HttpResponse\Header;
use App\ItemType\Select;
use App\Models\ItemCategory;
use App\Models\ItemSubcategory;
use App\Transformer\ItemSubcategory as ItemSubcategoryTransformer;
use Illuminate\Http\JsonResponse;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2023
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemSubcategoryController extends Controller
{
    public function index(
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $item_category_id
    ): JsonResponse {
        if ($this->hasViewAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.item-category'));
        }

        $item_type = Select::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense' => $this->itemSubcategoryCollection((int) $resource_type_id, (int) $resource_id, (int) $item_id, (int) $item_category_id),
            'game' => \App\HttpResponse\Response::notSupported(),
            default => throw new \OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    private function itemSubcategoryCollection(
        int $resource_type_id,
        int $resource_id,
        int $item_id,
        int $item_category_id
    ): JsonResponse {
        $cache_control = new \App\Cache\Control($this->user_id);
        $cache_control->setTtlOneWeek();

        $cache_collection = new \App\Cache\Response\Collection();
        $cache_collection->setFromCache($cache_control->getByKey(request()->getRequestUri()));

        if ($cache_control->isRequestCacheable() === false || $cache_collection->valid() === false) {
            $item_sub_category = (new ItemSubcategory())->paginatedCollection(
                $resource_type_id,
                $resource_id,
                $item_id,
                $item_category_id
            );

            if (count($item_sub_category) === 0) {
                $collection = [];
            } else {
                $collection = array_map(
                    static function ($category) {
                        return (new ItemSubcategoryTransformer($category))->asArray();
                    },
                    $item_sub_category
                );
            }

            $headers = new Header();
            $headers->add('X-Total-Count', 1);
            $headers->add('X-Count', 1);
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
        string $item_category_id = null,
        string $item_subcategory_id = null
    ): JsonResponse {
        if ($this->hasViewAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.item-subcategory'));
        }

        if ($item_category_id === null || $item_subcategory_id === null) {
            return \App\HttpResponse\Response::notFound(trans('entities.item-subcategory'));
        }

        $item_type = Select::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense' => $this->itemSubcategory((int) $resource_type_id, (int) $resource_id, (int) $item_id, (int) $item_category_id, (int) $item_subcategory_id),
            'game' => \App\HttpResponse\Response::notSupported(),
            default => throw new \OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    private function itemSubcategory(
        int $resource_type_id,
        int $resource_id,
        int $item_id,
        int $item_category_id,
        int $item_subcategory_id
    ): JsonResponse {
        $item_sub_category = (new ItemSubcategory())->single(
            $resource_type_id,
            $resource_id,
            $item_id,
            $item_category_id,
            $item_subcategory_id
        );

        if ($item_sub_category === null) {
            return \App\HttpResponse\Response::notFound(trans('entities.item-subcategory'));
        }

        $headers = new Header();
        $headers->item();

        return response()->json(
            (new ItemSubcategoryTransformer($item_sub_category))->asArray(),
            200,
            $headers->headers()
        );
    }

    public function optionsIndex(
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $item_category_id = null
    ): JsonResponse {
        if ($this->hasViewAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.item-category'));
        }

        if ($item_category_id === null) {
            return \App\HttpResponse\Response::notFound(trans('entities.item-subcategory'));
        }

        $item_type = Select::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense' => $this->optionsAllocatedExpenseCollection((int) $resource_type_id, (int) $item_category_id),
            'game' => \App\HttpResponse\Response::notSupported(),
            default => throw new \OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    private function optionsAllocatedExpenseCollection(
        int $resource_type_id,
        int $item_category_id
    ): JsonResponse {
        if ($this->hasViewAccessToResourceType($resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.item-category'));
        }

        $item_category = (new ItemCategory())->find($item_category_id);
        if ($item_category === null) {
            return \App\HttpResponse\Response::notFound(trans('entities.item-category'));
        }

        $response = new AllocatedExpenseCollection($this->permissions((int) $resource_type_id));

        return $response->setAllowedValuesForFields((new \App\Models\AllowedValue\Subcategory())->allowedValues($item_category->category_id))
            ->create()
            ->response();
    }

    public function optionsShow(
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $item_category_id = null,
        string $item_subcategory_id = null
    ): JsonResponse {
        if ($this->hasViewAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.item-subcategory'));
        }

        if ($item_category_id === null || $item_subcategory_id === null) {
            return \App\HttpResponse\Response::notFound(trans('entities.item-subcategory'));
        }

        $item_type = Select::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense' => $this->optionsItemSubcategoryShow((int) $resource_type_id, (int) $resource_id, (int) $item_id, (int) $item_category_id, (int) $item_subcategory_id),
            'game' => \App\HttpResponse\Response::notSupported(),
            default => throw new \OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    public function optionsItemSubcategoryShow(
        int $resource_type_id,
        int $resource_id,
        int $item_id,
        int $item_category_id,
        int $item_subcategory_id
    ): JsonResponse {
        $item_sub_category = (new ItemSubcategory())->single(
            $resource_type_id,
            $resource_id,
            $item_id,
            $item_category_id,
            $item_subcategory_id
        );

        if ($item_sub_category === null) {
            return \App\HttpResponse\Response::notFound(trans('entities.item-subcategory'));
        }

        $response = new ItemSubcategoryItem($this->permissions($resource_type_id));

        return $response->create()->response();
    }
}
