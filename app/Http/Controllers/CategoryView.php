<?php

namespace App\Http\Controllers;

use App\Models\Subcategory;
use App\Option\CategoryCollection;
use App\Option\CategoryItem;
Use App\Response\Cache;
use App\Response\Header\Header;
use App\Request\Parameter;
use App\Request\Route;
use App\Response\Header\Headers;
use App\Response\Pagination as UtilityPagination;
use App\Models\Category;
use App\Models\Transformers\Category as CategoryTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
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
        if ($this->viewAccessToResourceType((int) $resource_type_id) === false) {
            \App\Response\Responses::notFoundOrNotAccessible(trans('entities.resource-type'));
        }

        $cache_control = new Cache\Control(
            $this->writeAccessToResourceType((int) $resource_type_id),
            $this->user_id
        );
        $cache_control->setTtlOneMonth();

        $cache_collection = new Cache\Collection();
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

            $pagination = new UtilityPagination(request()->path(), $total);
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

            $collection = array_map(
                static function ($category) {
                    return (new CategoryTransformer($category))->asArray();
                },
                $categories
            );

            $headers = new Headers();
            $headers->collection($pagination_parameters, count($categories), $total)->
                addCacheControl($cache_control->visibility(), $cache_control->ttl())->
                addETag($collection)->
                addSearch(Parameter\Search::xHeader())->
                addSort(Parameter\Sort::xHeader());

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
        if ($this->viewAccessToResourceType((int) $resource_type_id) === false) {
            \App\Response\Responses::notFoundOrNotAccessible(trans('entities.category'));
        }

        $parameters = Parameter\Request::fetch(array_keys(Config::get('api.category.parameters.item')));

        $category = (new Category)->single(
            (int) $resource_type_id,
            (int) $category_id
        );

        if ($category === null) {
            return \App\Response\Responses::notFound(trans('entities.category'));
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
        if ($this->viewAccessToResourceType((int) $resource_type_id) === false) {
            \App\Response\Responses::notFoundOrNotAccessible(trans('entities.resource-type'));
        }

        $permissions = Route\Permission::resourceType(
            (int) $resource_type_id,
            $this->permitted_resource_types
        );

        $response = new CategoryCollection($permissions);

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
        if ($this->viewAccessToResourceType((int) $resource_type_id) === false) {
            \App\Response\Responses::notFoundOrNotAccessible(trans('entities.category'));
        }

        $permissions = Route\Permission::category(
            $resource_type_id,
            $category_id,
            $this->permitted_resource_types
        );

        $response = new CategoryItem($permissions);

        return $response->create()->response();
    }
}
