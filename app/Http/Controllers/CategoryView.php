<?php

namespace App\Http\Controllers;

use App\HttpResponse\Header;
use App\Models\Category;
use App\Models\Subcategory;
use App\HttpOptionResponse\CategoryCollection;
use App\HttpOptionResponse\CategoryItem;
use App\HttpRequest\Parameter;
use App\Transformer\Category as CategoryTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class CategoryView extends Controller
{
    protected bool $allow_entire_collection = true;

    /**
     * Return the categories collection
     *
     * @param string $resource_type_id
     *
     * @return JsonResponse
     */
    public function index($resource_type_id): JsonResponse
    {
        if ($this->hasViewAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Responses::notFoundOrNotAccessible(trans('entities.resource-type'));
        }

        $cache_control = new \App\Cache\Control(
            $this->hasWriteAccessToResourceType((int) $resource_type_id),
            $this->user_id
        );
        $cache_control->setTtlOneMonth();

        $cache_collection = new \App\Cache\Collection();
        $cache_collection->setFromCache($cache_control->getByKey(request()->getRequestUri()));

        if ($cache_control->isRequestCacheable() === false || $cache_collection->valid() === false) {

            $search_parameters = Parameter\Search::fetch(
                Config::get('api.category.searchable')
            );

            $sort_parameters = Parameter\Sort::fetch(
                Config::get('api.category.sortable')
            );

            $total = (new Category())->total(
                (int) $resource_type_id,
                $this->viewable_resource_types,
                $search_parameters
            );

            $pagination = new \App\HttpResponse\Pagination(request()->path(), $total);
            $pagination_parameters = $pagination->allowPaginationOverride($this->allow_entire_collection)->
                setSearchParameters($search_parameters)->
                setSortParameters($sort_parameters)->
                parameters();

            $categories = (new Category())->paginatedCollection(
                (int) $resource_type_id,
                $this->viewable_resource_types,
                $pagination_parameters['offset'],
                $pagination_parameters['limit'],
                $search_parameters,
                $sort_parameters
            );

            $last_updated = null;
            if (count($categories) && array_key_exists('last_updated', $categories[0])) {
                $last_updated = $categories[0]['last_updated'];
            }

            $collection = array_map(
                static function ($category) {
                    return (new CategoryTransformer($category))->asArray();
                },
                $categories
            );

            $headers = new Header();
            $headers
                ->collection($pagination_parameters, count($categories), $total)
                ->addCacheControl($cache_control->visibility(), $cache_control->ttl())
                ->addETag($collection)
                ->addSearch(Parameter\Search::xHeader())
                ->addSort(Parameter\Sort::xHeader());

            if ($last_updated !== null) {
                $headers->addLastUpdated($last_updated);
            }

            $cache_collection->create($total, $collection, $pagination_parameters, $headers->headers());
            $cache_control->putByKey(request()->getRequestUri(), $cache_collection->content());
        }

        return response()->json($cache_collection->collection(), 200, $cache_collection->headers());
    }

    /**
     * Return a single category
     *
     * @param $resource_type_id
     * @param $category_id
     *
     * @return JsonResponse
     */
    public function show($resource_type_id, $category_id): JsonResponse
    {
        if ($this->hasViewAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Responses::notFoundOrNotAccessible(trans('entities.category'));
        }

        $parameters = Parameter\Request::fetch(array_keys(Config::get('api.category.parameters-show')));

        $category = (new Category)->single(
            (int) $resource_type_id,
            (int) $category_id
        );

        if ($category === null) {
            return \App\HttpResponse\Responses::notFound(trans('entities.category'));
        }

        $subcategories = [];
        if (
            array_key_exists('include-subcategories', $parameters) === true &&
            $parameters['include-subcategories'] === true
        ) {
            $subcategories = (new Subcategory())->paginatedCollection(
                (int) $resource_type_id,
                (int) $category_id,
                0,
                100
            );
        }

        $headers = new Header();
        $headers->item();

        $parameters_header = Parameter\Request::xHeader();
        if ($parameters_header !== null) {
            $headers->addParameters($parameters_header);
        }

        return response()->json(
            (new CategoryTransformer($category, ['subcategories'=>$subcategories]))->asArray(),
            200,
            $headers->headers()
        );
    }

    /**
     * Generate the OPTIONS request for the category list
     *
     * @param $resource_type_id
     *
     * @return JsonResponse
     */
    public function optionsIndex($resource_type_id): JsonResponse
    {
        if ($this->hasViewAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Responses::notFoundOrNotAccessible(trans('entities.resource-type'));
        }

        $response = new CategoryCollection($this->permissions((int) $resource_type_id));

        return $response->create()->response();
    }

    /**
     * Generate the OPTIONS request for a specific category
     *
     * @param $resource_type_id
     * @param $category_id
     *
     * @return JsonResponse
     */
    public function optionsShow($resource_type_id, $category_id): JsonResponse
    {
        if ($this->hasViewAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Responses::notFoundOrNotAccessible(trans('entities.category'));
        }

        $response = new CategoryItem($this->permissions((int) $resource_type_id));

        return $response->create()->response();
    }
}
