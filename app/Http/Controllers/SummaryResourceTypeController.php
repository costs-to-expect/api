<?php

namespace App\Http\Controllers;

use App\Models\ResourceType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Summary controller for the resource-type routes
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SummaryResourceTypeController extends Controller
{
    /**
     * Return a summary of the resource types
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $summary = (new ResourceType())->totalCount($this->include_private);

        return response()->json(
            [
                'resource_types' => $summary
            ],
            200,
            ['X-Total-Count' => $summary]
        );
    }


    /**
     * Generate the OPTIONS request for the resource type summarys
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function optionsIndex(Request $request): JsonResponse
    {
        return $this->generateOptionsForIndex(
            [
                'description_localisation_string' => 'route-descriptions.summary-resource-type-GET-index',
                'parameters_config_string' => 'api.resource-type.summary-parameters.collection',
                'conditionals_config' => [],
                'sortable_config' => null,
                'enable_pagination' => false,
                'authentication_required' => false
            ]
        );
    }
}
