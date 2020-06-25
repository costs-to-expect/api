<?php

namespace App\Http\Controllers;

use App\Models\PermittedUser;
use App\Models\Transformers\PermittedUser as PermittedUserTransformer;
use App\Option\Get;
use App\Option\Post;
use App\Response\Cache;
use App\Request\Parameter;
use App\Request\Route;
use App\Response\Header\Headers;
use App\Response\Pagination as UtilityPagination;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

/**
 * Manage permitted users
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class PermittedUserController extends Controller
{
    protected bool $allow_entire_collection = true;

    /**
     * Return all the permitted users for the given resource type
     *
     * @param string $resource_type_id
     *
     * @return JsonResponse
     */
    public function index(string $resource_type_id): JsonResponse
    {
        Route\Validate::resourceType(
            $resource_type_id,
            $this->permitted_resource_types
        );

        $cache_control = new Cache\Control($this->user_id);
        $cache_control->setTtlOneMonth();

        $cache_collection = new Cache\Collection();
        $cache_collection->setFromCache($cache_control->get(request()->getRequestUri()));

        if ($cache_control->cacheable() === false || $cache_collection->valid() === false) {

            $search_parameters = Parameter\Search::fetch(
                array_keys(Config::get('api.permitted-user.searchable'))
            );

            $sort_parameters = Parameter\Sort::fetch(
                Config::get('api.permitted-user.sortable')
            );

            $total = (new PermittedUser())->totalCount(
                $resource_type_id,
                $search_parameters
            );

            $pagination = UtilityPagination::init(
                request()->path(),
                $total,
                10,
                $this->allow_entire_collection
            )->setSearchParameters($search_parameters)->setSortParameters($sort_parameters)->paging();

            $permitted_users = (new PermittedUser())->paginatedCollection(
                $resource_type_id,
                $pagination['offset'],
                $pagination['limit'],
                $search_parameters,
                $sort_parameters
            );

            $collection = array_map(
                static function ($permitted_user) {
                    return (new PermittedUserTransformer($permitted_user))->asArray();
                },
                $permitted_users
            );

            $headers = new Headers();
            $headers->collection($pagination, count($permitted_users), $total)->
                addCacheControl($cache_control->visibility(), $cache_control->ttl())->
                addETag($collection)->
                addSearch(Parameter\Search::xHeader())->
                addSort(Parameter\Sort::xHeader());

            $cache_collection->create($total, $collection, $pagination, $headers->headers());
            $cache_control->put(request()->getRequestUri(), $cache_collection->content());
        }

        return response()->json($cache_collection->collection(), 200, $cache_collection->headers());
    }

    /**
     * Generate the OPTIONS request for the permitted users collection
     *
     * @param string $resource_type_id
     *
     * @return JsonResponse
     */
    public function optionsIndex(string $resource_type_id): JsonResponse
    {
        Route\Validate::resourceType(
            $resource_type_id,
            $this->permitted_resource_types
        );

        $permissions = Route\Permission::resourceType(
            $resource_type_id,
            $this->permitted_resource_types
        );

        $get = Get::init()->
            setSortable('api.permitted-user.sortable')->
            setSearchable('api.permitted-user.searchable')->
            setPaginationOverride(true)->
            setAuthenticationStatus($permissions['view'])->
            setDescription('route-descriptions.permitted_user_GET_index')->
            option();

        $post = Post::init()->
            setFields('api.permitted-user.fields')->
            setDescription('route-descriptions.permitted_user_POST')->
            setAuthenticationStatus($permissions['manage'])->
            setAuthenticationRequired(true)->
            option();

        return $this->optionsResponse(
            $get + $post,
            200
        );
    }
}
