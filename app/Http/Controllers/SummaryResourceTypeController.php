<?php

namespace App\Http\Controllers;

use App\Models\ResourceType;
use App\Option\Get;
use Illuminate\Http\JsonResponse;

/**
 * Summary controller for the resource-type routes
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SummaryResourceTypeController extends Controller
{
    /**
     * Return a summary of the resource types
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $summary = (new ResourceType())->totalCount(
            $this->permitted_resource_types,
            $this->include_public
        );

        return response()->json(
            [
                'resource_types' => $summary
            ],
            200,
            ['X-Total-Count' => $summary]
        );
    }


    /**
     * Generate the OPTIONS request for the resource type summaries
     *
     * @return JsonResponse
     */
    public function optionsIndex(): JsonResponse
    {
        $get = Get::init()->
            setDescription('route-descriptions.summary-resource-type-GET-index')->
            setParameters('api.resource-type.summary-parameters.collection')->
            option();

        return $this->optionsResponse($get, 200);
    }
}
