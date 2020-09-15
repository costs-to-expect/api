<?php

namespace App\Http\Controllers;

use App\Option\ItemSubcategoryCollection;
use App\Option\ItemSubcategoryItem;
use App\Response\Cache;
use App\Response\Header\Header;
use App\Request\Route;
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
        Route\Validate::item(
            $resource_type_id,
            $resource_id,
            $item_id,
            $this->permitted_resource_types
        );

        $cache_control = new Cache\Control(
            $this->user_id,
            in_array($resource_type_id, $this->permitted_resource_types, true)
        );
        $cache_control->setTtlOneWeek();

        $cache_collection = new Cache\Collection();
        $cache_collection->setFromCache($cache_control->get(request()->getRequestUri()));

        if ($cache_control->cacheable() === false || $cache_collection->valid() === false) {

            $item_sub_category = (new ItemSubcategory())->paginatedCollection(
                $resource_type_id,
                $resource_id,
                $item_id,
                $item_category_id
            );

            if ($item_sub_category === null || (is_array($item_sub_category) && count($item_sub_category) === 0)) {
                $collection = [];
            } else {
                $collection = [(new ItemSubcategoryTransformer($item_sub_category[0]))->asArray()];
            }

            $headers = new Header();
            $headers->add('X-Total-Count', 1);
            $headers->add('X-Count', 1);
            $headers->addCacheControl($cache_control->visibility(), $cache_control->ttl());

            $cache_collection->create(count($collection), $collection, [], $headers->headers());
            $cache_control->put(request()->getRequestUri(), $cache_collection->content());
        }

        return response()->json($cache_collection->collection(), 200, $cache_collection->headers());
    }

    /**
     * Return a single item
     *
     * @param string $resource_id
     * @param string $resource_type_id
     * @param string $item_id
     * @param string $item_category_id
     * @param string $item_subcategory_id
     *
     * @return JsonResponse
     */
    public function show(
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $item_category_id,
        string $item_subcategory_id
    ): JsonResponse
    {
        Route\Validate::item(
            $resource_type_id,
            $resource_id,
            $item_id,
            $this->permitted_resource_types
        );

        if ($item_category_id === 'nill' || $item_subcategory_id === 'nill') {
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

    /**
     * Generate the OPTIONS request for the item list
     *
     * @param string $resource_type_id
     * @param string $resource_id
     * @param string $item_id
     * @param string $item_category_id
     *
     * @return JsonResponse
     */
    public function optionsIndex(
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $item_category_id
    ): JsonResponse
    {
        Route\Validate::item(
            $resource_type_id,
            $resource_id,
            $item_id,
            $this->permitted_resource_types
        );

        $permissions = Route\Permission::item(
            $resource_type_id,
            $resource_id,
            $item_id,
            $this->permitted_resource_types
        );

        if ($item_category_id === 'nill') {
            return \App\Response\Responses::notFound(trans('entities.item-subcategory'));
        }

        $item_category = (new ItemCategory())->find($item_category_id);
        if ($item_category === null) {
            return \App\Response\Responses::notFound(trans('entities.item-category'));
        }

        $response = new ItemSubcategoryCollection($permissions);

        return $response->setAllowedValues(
                (new \App\Option\AllowedValues\Subcategory())->allowedValues($item_category->category_id)
            )->
            create()->
            response();
    }

    /**
     * Generate the OPTIONS request for a specific item
     *
     * @param string $resource_id
     * @param string $resource_type_id
     * @param string $item_id
     * @param string $item_category_id
     * @param string $item_subcategory_id
     *
     * @return JsonResponse
     */
    public function optionsShow(
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $item_category_id,
        string $item_subcategory_id
    ): JsonResponse
    {
        Route\Validate::item(
            $resource_type_id,
            $resource_id,
            $item_id,
            $this->permitted_resource_types
        );

        $permissions = Route\Permission::item(
            $resource_type_id,
            $resource_id,
            $item_id,
            $this->permitted_resource_types
        );

        if ($item_category_id === 'nill' || $item_subcategory_id === 'nill') {
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

        $response = new ItemSubcategoryItem($permissions);

        return $response->create()->response();
    }
}
