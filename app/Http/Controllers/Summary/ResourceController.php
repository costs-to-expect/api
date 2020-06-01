<?php

namespace App\Http\Controllers\Summary;

use App\Http\Controllers\Controller;
use App\Option\Get;
use App\Utilities\Header;
use App\Utilities\RoutePermission;
use App\Validators\Route;
use App\Models\Summary\Resource;
use App\Validators\SearchParameters;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

/**
 * Summary controller for the resource routes
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceController extends Controller
{
    /**
     * Return a summary of the resources
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
            Config::get('api.resource.summary-searchable')
        );

        $summary = (new Resource())->totalCount(
            $resource_type_id,
            $this->permitted_resource_types,
            $this->include_public,
            $search_parameters
        );

        $headers = new Header();
        $headers->add('X-Total-Count', $summary);
        $headers->add('X-Count', $summary);

        return response()->json(
            [
                'resources' => $summary
            ],
            200,
            $headers->headers()
        );
    }


    /**
     * Generate the OPTIONS request for the resource summary
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
            setParameters('api.resource.summary-parameters')->
            setDescription('route-descriptions.summary-resource-GET-index')->
            setAuthenticationStatus($permissions['view'])->
            setSearchable('api.resource.summary-searchable')->
            option();

        return $this->optionsResponse($get, 200);
    }
}
