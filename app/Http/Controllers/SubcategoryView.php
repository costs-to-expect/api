<?php

namespace App\Http\Controllers;

use App\Option\Delete;
use App\Option\Get;
use App\Option\Patch;
use App\Option\Post;
use App\Response\Cache;
use App\Response\Header\Header;
use App\Request\Parameter;
use App\Request\Route;
use App\Response\Header\Headers;
use App\Response\Pagination as UtilityPagination;
use App\Models\Subcategory;
use App\Models\Transformers\Subcategory as SubcategoryTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

/**
 * Manage category sub categories
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SubcategoryView extends Controller
{
    protected bool $allow_entire_collection = true;

    /**
     * Return all the sub categories assigned to the given category
     *
     * @param $resource_type_id
     * @param $category_id
     *
     * @return JsonResponse
     */
    public function index($resource_type_id, $category_id): JsonResponse
    {
        Route\Validate::category(
            (int) $resource_type_id,
            (int) $category_id,
            $this->permitted_resource_types
        );

        $cache_control = new Cache\Control($this->user_id);
        $cache_control->setTtlOneMonth();

        $cache_collection = new Cache\Collection();
        $cache_collection->setFromCache($cache_control->get(request()->getRequestUri()));

        if ($cache_control->cacheable() === false || $cache_collection->valid() === false) {

            $search_parameters = Parameter\Search::fetch(
                array_keys(Config::get('api.subcategory.searchable'))
            );

            $sort_parameters = Parameter\Sort::fetch(
                Config::get('api.subcategory.sortable')
            );

            $total = (new Subcategory())->totalCount(
                (int)$resource_type_id,
                (int)$category_id,
                $search_parameters
            );

            $pagination = new UtilityPagination(request()->path(), $total);
            $pagination_parameters = $pagination->allowPaginationOverride($this->allow_entire_collection)->
                setSearchParameters($search_parameters)->
                setSortParameters($sort_parameters)->
                parameters();

            $subcategories = (new Subcategory())->paginatedCollection(
                (int)$resource_type_id,
                (int)$category_id,
                $pagination_parameters['offset'],
                $pagination_parameters['limit'],
                $search_parameters,
                $sort_parameters
            );

            $collection = array_map(
                static function ($subcategory) {
                    return (new SubcategoryTransformer($subcategory))->asArray();
                },
                $subcategories
            );

            $headers = new Headers();
            $headers->collection($pagination_parameters, count($subcategories), $total)->
                addCacheControl($cache_control->visibility(), $cache_control->ttl())->
                addETag($collection)->
                addSearch(Parameter\Search::xHeader())->
                addSort(Parameter\Sort::xHeader());

            $cache_collection->create($total, $collection, $pagination_parameters, $headers->headers());
            $cache_control->put(request()->getRequestUri(), $cache_collection->content());
        }

        return response()->json($cache_collection->collection(), 200, $cache_collection->headers());
    }

    /**
     * Return a single sub category
     *
     * @param $resource_type_id
     * @param $category_id
     * @param $subcategory_id
     *
     * @return JsonResponse
     */
    public function show(
        $resource_type_id,
        $category_id,
        $subcategory_id
    ): JsonResponse
    {
        Route\Validate::subcategory(
            (int) $resource_type_id,
            (int) $category_id,
            (int) $subcategory_id,
            $this->permitted_resource_types
        );

        $subcategory = (new Subcategory())->single(
            $category_id,
            $subcategory_id
        );

        if ($subcategory === null) {
            \App\Response\Responses::notFound();
        }

        $headers = new Header();
        $headers->item();

        return response()->json(
            (new SubcategoryTransformer($subcategory))->asArray(),
            200,
            $headers->headers()
        );
    }

    /**
     * Generate the OPTIONS request for the sub categories list
     *
     * @param $resource_type_id
     * @param $category_id
     *
     * @return JsonResponse
     */
    public function optionsIndex($resource_type_id, $category_id): JsonResponse
    {
        Route\Validate::category(
            (int) $resource_type_id,
            (int) $category_id,
            $this->permitted_resource_types
        );

        $permissions = Route\Permission::category(
            (int) $resource_type_id,
            (int) $category_id,
            $this->permitted_resource_types
        );

        $get = Get::init()->
            setSortable('api.subcategory.sortable')->
            setSearchable('api.subcategory.searchable')->
            setPaginationOverride(true)->
            setParameters('api.subcategory.parameters.collection')->
            setDescription('route-descriptions.sub_category_GET_index')->
            setAuthenticationStatus($permissions['view'])->
            option();

        $post = Post::init()->
            setFields('api.subcategory.fields')->
            setDescription('route-descriptions.sub_category_POST')->
            setAuthenticationRequired(true)->
            setAuthenticationStatus($permissions['manage'])->
            option();

        return $this->optionsResponse(
            $get + $post,
            200
        );
    }

    /**
     * Generate the OPTIONS request for the specific sub category
     *
     * @param $resource_type_id
     * @param $category_id
     * @param $subcategory_id
     *
     * @return JsonResponse
     */
    public function optionsShow(
        $resource_type_id,
        $category_id,
        $subcategory_id
    ): JsonResponse
    {
        Route\Validate::subcategory(
            (int) $resource_type_id,
            (int) $category_id,
            (int) $subcategory_id,
            $this->permitted_resource_types
        );

        $permissions = Route\Permission::subcategory(
            (int) $resource_type_id,
            (int) $category_id,
            (int) $subcategory_id,
            $this->permitted_resource_types
        );

        $get = Get::init()->
            setParameters('api.subcategory.parameters.item')->
            setDescription('route-descriptions.sub_category_GET_show')->
            setAuthenticationStatus($permissions['view'])->
            option();

        $delete = Delete::init()->
            setDescription('route-descriptions.sub_category_DELETE')->
            setAuthenticationRequired(true)->
            setAuthenticationStatus($permissions['manage'])->
            option();

        $patch = Patch::init()->
            setFields('api.subcategory.fields')->
            setDescription('route-descriptions.sub_category_PATCH')->
            setAuthenticationRequired(true)->
            setAuthenticationStatus($permissions['manage'])->
            option();

        return $this->optionsResponse(
            $get + $delete + $patch,
            200
        );
    }
}
