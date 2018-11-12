<?php

namespace App\Http\Controllers;

use App\Http\Parameters\Route\Validate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

/**
 * Expanded summaries
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ExpandedSummaryController extends Controller
{
    /**
     * Return the TCO for the resource
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     *
     * @return JsonResponse
     */
    public function categories(
        Request $request,
        string $resource_type_id,
        string $resource_id
    ): JsonResponse {
        Validate::resourceRoute($resource_type_id, $resource_id);

        return response()->json(
            [

            ],
            200
        );
    }

    /**
     * Generate the OPTIONS request for the TCO
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     */
    public function optionsCategories(
        Request $request,
        string $resource_type_id,
        string $resource_id
    ) {
        Validate::resourceRoute($resource_type_id, $resource_id);

        $routes = [
            'GET' => [
                'description' => Config::get('api.descriptions.summary.GET_expanded_categories'),
                'authenticated' => false,
                'parameters' => []
            ]
        ];

        $this->optionsResponse($routes);
    }
}
