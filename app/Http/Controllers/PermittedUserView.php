<?php

namespace App\Http\Controllers;

use App\Models\PermittedUser;
use App\Option\PermittedUserCollection;
use App\Request\Parameter;
use App\Response\Header\Header;
use App\Response\Pagination as UtilityPagination;
use App\Transformers\PermittedUser as PermittedUserTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

/**
 * Manage permitted users
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2021
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class PermittedUserView extends Controller
{
    protected bool $allow_entire_collection = true;

    public function index(string $resource_type_id): JsonResponse
    {
        if ($this->viewAccessToResourceType((int) $resource_type_id) === false) {
            \App\Response\Responses::notFoundOrNotAccessible(trans('entities.resource-type'));
        }

        $cache_control = new \App\Cache\Control(
            $this->writeAccessToResourceType((int) $resource_type_id),
            $this->user_id
        );
        $cache_control->setTtlOneMonth();

        $cache_collection = new \App\Cache\Collection();
        $cache_collection->setFromCache($cache_control->getByKey(request()->getRequestUri()));

        if ($cache_control->isRequestCacheable() === false || $cache_collection->valid() === false) {

            $search_parameters = Parameter\Search::fetch(
                Config::get('api.permitted-user.searchable')
            );

            $sort_parameters = Parameter\Sort::fetch(
                Config::get('api.permitted-user.sortable')
            );

            $total = (new PermittedUser())->totalCount(
                $resource_type_id,
                $search_parameters
            );

            $pagination = new UtilityPagination(request()->path(), $total);
            $pagination_parameters = $pagination->allowPaginationOverride($this->allow_entire_collection)->
                setSearchParameters($search_parameters)->
                setSortParameters($sort_parameters)->
                parameters();

            $permitted_users = (new PermittedUser())->paginatedCollection(
                $resource_type_id,
                $pagination_parameters['offset'],
                $pagination_parameters['limit'],
                $search_parameters,
                $sort_parameters
            );

            $collection = array_map(
                static function ($permitted_user) {
                    return (new PermittedUserTransformer($permitted_user))->asArray();
                },
                $permitted_users
            );

            $headers = new Header();
            $headers->collection($pagination_parameters, count($permitted_users), $total)->
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
     * Generate the OPTIONS request for the permitted users collection
     *
     * @param string $resource_type_id
     *
     * @return JsonResponse
     */
    public function optionsIndex(string $resource_type_id): JsonResponse
    {
        if ($this->viewAccessToResourceType((int) $resource_type_id) === false) {
            \App\Response\Responses::notFoundOrNotAccessible(trans('entities.resource-type'));
        }

        $response = new PermittedUserCollection($this->permissions((int) $resource_type_id));

        return $response->create()->response();
    }
}
