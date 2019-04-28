<?php

namespace App\Http\Controllers;

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

    }


    /**
     * Generate the OPTIONS request for summary of the access log
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function optionsIndex(Request $request): JsonResponse
    {
        return $this->generateOptionsForIndex(
            [
                'description_localisation' => 'route-descriptions.summary-resource-type-GET-index',
                'parameters_config' => 'api.resource-type.summary-parameters.collection',
                'conditionals' => [],
                'pagination' => false,
                'authenticated' => false
            ]
        );
    }
}
