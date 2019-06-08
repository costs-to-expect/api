<?php

namespace App\Http\Controllers;

use App\Validators\Request\Route;
use App\Models\Resource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Summary controller for the resource routes
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SummaryResourceController extends Controller
{
    /**
     * Return a summary of the resources
     *
     * @param Request $request
     * @param string $resource_type_id
     *
     * @return JsonResponse
     */
    public function index(Request $request, string $resource_type_id): JsonResponse
    {
        Route::resourceTypeRoute($resource_type_id);

        $summary = (new Resource())->totalCount($resource_type_id, $this->include_private);

        return response()->json(
            [
                'resources' => $summary
            ],
            200,
            ['X-Total-Count' => $summary]
        );
    }


    /**
     * Generate the OPTIONS request for the resource summary
     *
     * @param Request $request
     * @param string $resource_type_id
     *
     * @return JsonResponse
     */
    public function optionsIndex(Request $request, string $resource_type_id): JsonResponse
    {
        Route::resourceTypeRoute($resource_type_id);

        return $this->generateOptionsForIndex(
            [
                'description_localisation_string' => 'route-descriptions.summary-resource-GET-index',
                'parameters_config_string' => 'api.resource.summary-parameters.collection',
                'conditionals_config' => [],
                'sortable_config' => null,
                'searchable_config' => null,
                'enable_pagination' => false,
                'authentication_required' => false
            ]
        );
    }
}
