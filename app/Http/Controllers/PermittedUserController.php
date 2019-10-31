<?php

namespace App\Http\Controllers;

use App\Option\Get;
use App\Option\Post;
use App\Utilities\RoutePermission;
use App\Validators\Request\Route;
use Illuminate\Http\JsonResponse;

/**
 * Manage permitted users
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class PermittedUserController extends Controller
{
    protected $allow_entire_collection = true;

    /**
     * Generate the OPTIONS request for the resource list
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
