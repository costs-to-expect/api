<?php

namespace App\Http\Controllers;

use App\Option\Get;
use App\Utilities\Header;
use App\Utilities\RoutePermission;
use App\Validators\Request\Parameters;
use App\Validators\Request\Route;
use App\Models\Category;
use App\Models\ResourceTypeItem;
use App\Models\SubCategory;
use App\Models\Transformers\ResourceTypeItem as ResourceTypeItemTransformer;
use App\Utilities\Pagination as UtilityPagination;
use App\Validators\Request\SearchParameters;
use App\Validators\Request\SortParameters;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

/**
 * View items for all resources for a resource type
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceTypeItemController extends Controller
{
    private $conditional_get_parameters = [];

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
        Route::resourceType(
            $resource_type_id,
            $this->permitted_resource_types
        );

        $this->setItemInterface($resource_type_id);
        $resource_type_item_model = $this->item_interface->resourceTypeItemModel();

        $collection_parameters = Parameters::fetch(array_keys($this->item_interface->collectionParameters()));

        $sort_fields = SortParameters::fetch(
            $this->item_interface->sortParameters()
        );

        $search_conditions = SearchParameters::fetch(
            $this->item_interface->searchParameters()
        );

        $total = $resource_type_item_model->totalCount(
            $resource_type_id,
            $collection_parameters,
            $search_conditions
        );

        $pagination = UtilityPagination::init($request->path(), $total)
            ->setParameters()
            ->paging();

        $items = $resource_type_item_model->paginatedCollection(
            $resource_type_id,
            $pagination['offset'],
            $pagination['limit'],
            $collection_parameters,
            $sort_fields,
            $search_conditions
        );

        $headers = new Header();
        $headers->collection($pagination, count($items), $total);

        $sort_header = SortParameters::xHeader();
        if ($sort_header !== null) {
            $headers->addSort($sort_header);
        }

        $search_header = SearchParameters::xHeader();
        if ($search_header !== null) {
            $headers->addSearch($search_header);
        }

        $parameters_header = Parameters::xHeader();
        if ($parameters_header !== null) {
            $headers->addParameters($parameters_header);
        }

        return response()->json(
            array_map(
                function($item) {
                    return $this->item_interface->resourceTypeItemTransformer($item)->toArray();
                },
                $items
            ),
            200,
            $headers->headers()
        );
    }

    /**
     * Generate the OPTIONS request for the items list
     *
     * @param string $resource_type_id
     *
     * @return JsonResponse
     */
    public function optionsIndex(string $resource_type_id): JsonResponse
    {
        Route::resourceType(
            $resource_type_id,
            $this->permitted_resource_types
        );

        $this->setItemInterface($resource_type_id);

        $permissions = RoutePermission::resourceType(
            $resource_type_id,
            $this->permitted_resource_types
        );

        $this->conditionalGetParameters(
            $resource_type_id,
            ['category']
        );

        $get = Get::init()->
            setSortable( $this->item_interface->resourceTypeItemSortParametersConfig())->
            setSearchable($this->item_interface->resourceTypeItemSearchParametersConfig())->
            setPagination(true)->
            setParameters($this->item_interface->resourceTypeItemCollectionParametersConfig())->
            setConditionalParameters($this->conditional_get_parameters)->
            setDescription('route-descriptions.resource_type_item_GET_index')->
            setAuthenticationStatus($permissions['view'])->
            option();

        return $this->optionsResponse($get, 200);
    }

    /**
     * Fetch the conditional GET parameters reasy to be be merged with the
     * GET parameters data array, useful for defining dynamic values such as
     * allowed values
     *
     * @param integer $resource_type_id
     * @param array $collection_parameters
     */
    private function conditionalGetParameters($resource_type_id, array $collection_parameters)
    {
        $this->conditional_get_parameters = [
            'year' => [
                'allowed_values' => []
            ],
            'month' => [
                'allowed_values' => []
            ],
            'category' => [
                'allowed_values' => []
            ],
            'subcategory' => [
                'allowed_values' => []
            ]
        ];

        for ($i=2013; $i <= intval(date('Y')); $i++) {
            $this->conditional_get_parameters['year']['allowed_values'][$i] = [
                'value' => $i,
                'name' => $i,
                'description' => trans('resource-type-item/allowed-values.description-prefix-year') . $i
            ];
        }

        for ($i=1; $i < 13; $i++) {
            $this->conditional_get_parameters['month']['allowed_values'][$i] = [
                'value' => $i,
                'name' => date("F", mktime(0, 0, 0, $i, 10)),
                'description' => trans('resource-type-item/allowed-values.description-prefix-month') .
                    date("F", mktime(0, 0, 0, $i, 1))
            ];
        }

        $categories = (new Category())->paginatedCollection(
            (int) $resource_type_id,
            $this->permitted_resource_types,
            $this->include_public,
            0,
            100
        );
        array_map(
            function($category) {
                $this->conditional_get_parameters['category']['allowed_values'][$this->hash->encode('category', $category['category_id'])] = [
                    'value' => $this->hash->encode('category', $category['category_id']),
                    'name' => $category['category_name'],
                    'description' => trans('resource-type-item/allowed-values.description-prefix-category') .
                        $category['category_name'] . trans('resource-type-item/allowed-values.description-suffix-category')
                ];
            },
            $categories
        );

        if (array_key_exists('category', $collection_parameters) === true) {
            $subcategories = (new SubCategory())->paginatedCollection($collection_parameters['category']);

            array_map(
                function($subcategory) {
                    $this->conditional_get_parameters['subcategory']['allowed_values'][$this->hash->encode('subcategory', $subcategory['id'])] = [
                        'value' => $this->hash->encode('subcategory', $subcategory['id']),
                        'name' => $subcategory['name'],
                        'description' => trans('item-type-allocated-expense/allowed-values.description-prefix-subcategory') .
                            $subcategory['name'] . trans('item-type-allocated-expense/allowed-values.description-suffix-subcategory')
                    ];
                },
                $subcategories
            );
          }
    }
}
