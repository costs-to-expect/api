<?php

namespace App\Http\Controllers;

use App\Http\Parameters\Get;
use App\Http\Parameters\Route\Validate;
use App\Models\ResourceTypeItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Summary for resource type items route
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SummaryResourceTypeItemController extends Controller
{
    private $resource_type_id;

    /**
     * Return the TCO for all the resources within the resource type
     *
     * @param Request $request
     * @param string $resource_type_id
     *
     * @return JsonResponse
     */
    public function index(Request $request, string $resource_type_id): JsonResponse
    {
        Validate::resourceTypeRoute($resource_type_id);

        $this->resource_type_id = $resource_type_id;

        $collection_parameters = Get::parameters([
            'include-categories',
            'include-subcategories'
        ]);

        return $this->summary();
    }

    /**
     * Return the total summary for all the resources in the resource type
     *
     * @return JsonResponse
     */
    private function summary(): JsonResponse
    {
        $summary = (new ResourceTypeItem())->summary($this->resource_type_id);

        return response()->json(
            [
                'total' => number_format($summary[0]['actualised_total'], 2, '.', '')
            ],
            200,
            ['X-Total-Count' => 1]
        );
    }

    /**
     * Generate the OPTIONS request for items summary route
     *
     * @param Request $request
     * @param string $resource_type_id
     *
     * @return JsonResponse
     *
     */
    public function optionsIndex(Request $request, string $resource_type_id): JsonResponse
    {
        Validate::resourceTypeRoute($resource_type_id);

        return $this->generateOptionsForIndex(
            [
                'description_localisation' => 'route-descriptions.summary-resource-type-item-GET-index',
                'parameters_config' => [],
                'conditionals' => [],
                'pagination' => false,
                'authenticated' => false
            ]
        );
    }
}
