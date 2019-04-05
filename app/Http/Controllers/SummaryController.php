<?php

namespace App\Http\Controllers;

use App\Http\Parameters\Route\Validate;
use App\Models\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Resource summary
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
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
        Validate::resourceRoute($resource_type_id, $resource_id);

        $summary = (new Item())->summary($resource_type_id, $resource_id);

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
     */
    public function optionsTco(Request $request, string $resource_type_id, string $resource_id)
    {
        Validate::resourceRoute($resource_type_id, $resource_id);

        return $this->generateOptionsForIndex(
            [
                'description_localisation' => 'route-descriptions.summary_GET_tco',
                'parameters_config' => [],
                'conditionals' => [],
                'pagination' => false,
                'authenticated' => false
            ]
        );
    }
}
