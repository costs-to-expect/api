<?php

namespace App\Http\Controllers;

use App\Http\Parameters\Route\Validate;
use App\Models\ResourceTypeItem;
use App\Models\Transformers\ResourceTypeItem as ResourceTypeItemTransformer;
use App\Utilities\Pagination as UtilityPagination;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * View items for all resources for a resource type
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceTypeItemController extends Controller
{
    protected $collection_parameters = [];

    /**
     * Return all the items based on the set filter options
     *
     * @param Request $request
     * @param string $resource_type_id
     *
     * @return JsonResponse
     */
    public function index(Request $request, string $resource_type_id): JsonResponse
    {
        Validate::resourceTypeRoute($resource_type_id);

        $total = (new ResourceTypeItem())->totalCount(
            $resource_type_id,
            $this->collection_parameters
        );

        $pagination = UtilityPagination::init($request->path(), $total)
            ->setParameters()
            ->paging();

        $items = (new ResourceTypeItem())->paginatedCollection(
            $resource_type_id,
            $pagination['offset'],
            $pagination['limit'],
            $this->collection_parameters
        );

        $headers = [
            'X-Count' => count($items),
            'X-Total-Count' => $total,
            'X-Link-Previous' => $pagination['links']['previous'],
            'X-Link-Next' => $pagination['links']['next']
        ];

        return response()->json(
            array_map(
                function($item) {
                    return (new ResourceTypeItemTransformer($item))->toArray();
                },
                $items
            ),
            200,
            $headers
        );
    }

    /**
     * Generate the OPTIONS request for the items list
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
                'description_localisation' => 'route-descriptions.resource_type_item_GET_index',
                'parameters_config' => 'api.resource-type-item.parameters.collection',
                'conditionals' => [],
                'pagination' => true,
                'authenticated' => false
            ]
        );
    }
}
