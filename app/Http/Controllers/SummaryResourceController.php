<?php

namespace App\Http\Controllers;

use App\Http\Parameters\Route\Validate;
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
        Validate::resourceTypeRoute($resource_type_id);

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
        Validate::resourceTypeRoute($resource_type_id);

        return $this->generateOptionsForIndex(
            [
                'description_localisation' => 'route-descriptions.summary-resource-GET-index',
                'parameters_config' => 'api.resource.summary-parameters.collection',
                'conditionals' => [],
                'sortable_config' => null,
                'pagination' => false,
                'authenticated' => false
            ]
        );
    }
}
