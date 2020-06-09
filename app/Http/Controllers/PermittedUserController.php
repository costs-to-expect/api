<?php

namespace App\Http\Controllers;

use App\Models\PermittedUser;
use App\Models\Transformers\PermittedUser as PermittedUserTransformer;
use App\Option\Get;
use App\Option\Post;
use App\Response\Header\Header;
use App\Utilities\Pagination as UtilityPagination;
use App\Utilities\RoutePermission;
use App\Validators\Route;
use App\Validators\SearchParameters;
use App\Validators\SortParameters;
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
        Route::resourceType(
            $resource_type_id,
            $this->permitted_resource_types
        );

        $search_parameters = SearchParameters::fetch(
            array_keys(Config::get('api.permitted-user.searchable'))
        );

        $total = (new PermittedUser())->totalCount(
            $resource_type_id,
            $search_parameters
        );

        $sort_parameters = SortParameters::fetch(
            Config::get('api.permitted-user.sortable')
        );

        $pagination = UtilityPagination::init(
                request()->path(),
                $total,
                10,
                $this->allow_entire_collection
            )->
            setSearchParameters($search_parameters)->
            setSortParameters($sort_parameters)->
            paging();

        $permitted_users = (new PermittedUser())->paginatedCollection(
            $resource_type_id,
            $pagination['offset'],
            $pagination['limit'],
            $search_parameters,
            $sort_parameters
        );

        $headers = new Header();
        $headers->collection($pagination, count($permitted_users), $total);

        $sort_header = SortParameters::xHeader();
        if ($sort_header !== null) {
            $headers->addSort($sort_header);
        }

        $search_header = SearchParameters::xHeader();
        if ($search_header !== null) {
            $headers->addSearch($search_header);
        }

        return response()->json(
            array_map(
                function($permitted_user) {
                    return (new PermittedUserTransformer($permitted_user))->toArray();
                },
                $permitted_users
            ),
            200,
            $headers->headers()
        );
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
        Route::resourceType(
            $resource_type_id,
            $this->permitted_resource_types
        );

        $permissions = RoutePermission::resourceType(
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
