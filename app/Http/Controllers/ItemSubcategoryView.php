<?php

namespace App\Http\Controllers;

use App\Entity\Item\Entity;
use App\Option\ItemSubcategoryCollection;
use App\Option\ItemSubcategoryItem;
use App\Response\Cache;
use App\Response\Header\Header;
use App\Models\ItemCategory;
use App\Models\ItemSubcategory;
use App\Models\Transformers\ItemSubcategory as ItemSubcategoryTransformer;
use Illuminate\Http\JsonResponse;

/**
 * Manage the category for an item row
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemSubcategoryView extends Controller
{
    /**
     * Return the sub category assigned to an item
     *
     * @param string $resource_type_id
     * @param string $resource_id
     * @param string $item_id
     * @param string $item_category_id
     *
     * @return JsonResponse
     */
    public function index(
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $item_category_id
    ): JsonResponse
    {
        if ($this->viewAccessToResourceType((int) $resource_type_id) === false) {
            \App\Response\Responses::notFoundOrNotAccessible(trans('entities.item-category'));
        }

        $cache_control = new Cache\Control(
            $this->writeAccessToResourceType((int) $resource_type_id),
            $this->user_id
        );
        $cache_control->setTtlOneWeek();

        $cache_collection = new Cache\Collection();
        $cache_collection->setFromCache($cache_control->getByKey(request()->getRequestUri()));

        if ($cache_control->isRequestCacheable() === false || $cache_collection->valid() === false) {

            $item_sub_category = (new ItemSubcategory())->paginatedCollection(
                $resource_type_id,
                $resource_id,
                $item_id,
                $item_category_id
            );

            if ($item_sub_category === null || (is_array($item_sub_category) && count($item_sub_category) === 0)) {
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
    ): JsonResponse
    {
        if ($this->viewAccessToResourceType((int) $resource_type_id) === false) {
            \App\Response\Responses::notFoundOrNotAccessible(trans('entities.item-subcategory'));
        }

        if ($item_category_id === null || $item_subcategory_id === null) {
            return \App\Response\Responses::notFound(trans('entities.item-subcategory'));
        }

        $item_sub_category = (new ItemSubcategory())->single(
            $resource_type_id,
            $resource_id,
            $item_id,
            $item_category_id,
            $item_subcategory_id
        );

        if ($item_sub_category === null) {
            return \App\Response\Responses::notFound(trans('entities.item-subcategory'));
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
    ): JsonResponse
    {
        if ($this->viewAccessToResourceType((int) $resource_type_id) === false) {
            \App\Response\Responses::notFoundOrNotAccessible(trans('entities.item-category'));
        }

        if ($item_category_id === null) {
            return \App\Response\Responses::notFound(trans('entities.item-subcategory'));
        }

        $item_category = (new ItemCategory())->find($item_category_id);
        if ($item_category === null) {
            return \App\Response\Responses::notFound(trans('entities.item-category'));
        }

        $response = new ItemSubcategoryCollection($this->permissions((int) $resource_type_id));

        return $response
            ->setEntity(Entity::item($resource_type_id))
            ->setAllowedValues(
                (new \App\Option\AllowedValue\Subcategory())->allowedValues($item_category->category_id)
            )
            ->create()
            ->response();
    }

    public function optionsShow(
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $item_category_id = null,
        string $item_subcategory_id = null
    ): JsonResponse
    {
        if ($this->viewAccessToResourceType((int) $resource_type_id) === false) {
            \App\Response\Responses::notFoundOrNotAccessible(trans('entities.item-subcategory'));
        }

        if ($item_category_id === null || $item_subcategory_id === null) {
            return \App\Response\Responses::notFound(trans('entities.item-subcategory'));
        }

        $item_sub_category = (new ItemSubcategory())->single(
            $resource_type_id,
            $resource_id,
            $item_id,
            $item_category_id,
            $item_subcategory_id
        );

        if ($item_sub_category === null) {
            return \App\Response\Responses::notFound(trans('entities.item-subcategory'));
        }

        $response = new ItemSubcategoryItem($this->permissions((int) $resource_type_id));

        return $response->create()->response();
    }
}
