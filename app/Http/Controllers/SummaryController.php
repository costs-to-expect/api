<?php

namespace App\Http\Controllers;

use App\Http\Route\Validators\Resource as ResourceRouteValidator;
use App\Models\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

/**
 * Resource summary
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SummaryController extends Controller
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
    public function tco(Request $request, string $resource_type_id, string $resource_id): JsonResponse
    {
        if (ResourceRouteValidator::validate($resource_type_id, $resource_id) === false) {
            return $this->returnResourceNotFound();
        }

        $summary = (new Item())->summary($resource_type_id,$resource_id);

        $headers = [
            'X-Total-Count' => 1
        ];

        return response()->json(
            [
                'total' => number_format($summary, 2, '.', '')
            ],
            200,
            $headers
        );
    }

    /**
     * Generate the OPTIONS request for the TCO
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function optionsTco(Request $request, string $resource_type_id, string $resource_id): JsonResponse
    {
        if (ResourceRouteValidator::validate($resource_type_id, $resource_id) === false) {
            return $this->returnResourceNotFound();
        }

        $routes = [
            'GET' => [
                'description' => Config::get('api.descriptions.summary.GET_tco'),
                'authenticated' => false,
                'parameters' => []
            ]
        ];

        $options_response = $this->generateOptionsResponse($routes);

        return response()->json(
            $options_response['verbs'],
            $options_response['http_status_code'],
            $options_response['headers']
        );
    }

    /**
     * Return the category/sub category summary for a resource
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     *
     * @return JsonResponse
     */
    public function category(Request $request, string $resource_type_id, string $resource_id): JsonResponse
    {
        if (ResourceRouteValidator::validate($resource_type_id, $resource_id) === false) {
            return $this->returnResourceNotFound();
        }

        $summary = (new Item())->categorySummary($resource_type_id, $resource_id);

        $headers = [
            'X-Total-Count' => count($summary)
        ];

        return response()->json(
            $summary,
            200,
            $headers
        );
    }
}
