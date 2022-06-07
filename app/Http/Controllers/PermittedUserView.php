<?php

namespace App\Http\Controllers;

use App\HttpResponse\Header;
use App\Models\PermittedUser;
use App\HttpOptionResponse\PermittedUserCollection;
use App\HttpOptionResponse\PermittedUserItem;
use App\HttpRequest\Parameter;
use App\Transformer\PermittedUser as PermittedUserTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class PermittedUserView extends Controller
{
    protected bool $allow_entire_collection = true;

    public function index(string $resource_type_id): JsonResponse
    {
        if ($this->viewAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Responses::notFoundOrNotAccessible(trans('entities.resource-type'));
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

            $pagination = new \App\HttpResponse\Pagination(request()->path(), $total);
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

    public function show(
        string $resource_type_id,
        string $permitted_user_id
    ): JsonResponse
    {
        if ($this->viewAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Responses::notFoundOrNotAccessible(trans('entities.resource'));
        }

        $permitted_user = (new PermittedUser())->single($resource_type_id, $permitted_user_id);

        if ($permitted_user === null) {
            return \App\HttpResponse\Responses::notFound(trans('entities.permitted-user'));
        }

        $headers = new Header();
        $headers->item();

        return response()->json(
            (new PermittedUserTransformer($permitted_user))->asArray(),
            200,
            $headers->headers()
        );
    }

    public function optionsIndex(string $resource_type_id): JsonResponse
    {
        if ($this->viewAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Responses::notFoundOrNotAccessible(trans('entities.resource-type'));
        }

        $response = new PermittedUserCollection($this->permissions((int) $resource_type_id));

        return $response->create()->response();
    }

    public function optionsShow(string $resource_type_id, string $permitted_user_id): JsonResponse
    {
        if ($this->viewAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Responses::notFoundOrNotAccessible(trans('entities.resource'));
        }

        $permitted_user = (new PermittedUser())->single(
            $resource_type_id,
            $permitted_user_id
        );

        if ($permitted_user === null) {
            return \App\HttpResponse\Responses::notFound(trans('entities.permitted-user'));
        }

        $response = new PermittedUserItem($this->permissions((int) $resource_type_id));

        return $response->create()->response();
    }
}
