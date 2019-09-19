<?php

namespace App\Http\Controllers;

use App\Option\Get;
use App\Utilities\Header;
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

        $collection_parameters = Parameters::fetch([
            'include-categories',
            'include-subcategories',
            'include-unpublished',
            'year',
            'month',
            'category',
            'subcategory'
        ]);

        $sort_fields = SortParameters::fetch([
            'description',
            'total',
            'actualised_total',
            'effective_date',
            'created'
        ]);

        $search_conditions = SearchParameters::fetch([
            'description'
        ]);

        $total = (new ResourceTypeItem())->totalCount(
            $resource_type_id,
            $collection_parameters,
            $search_conditions
        );

        $pagination = UtilityPagination::init($request->path(), $total)
            ->setParameters()
            ->paging();

        $items = (new ResourceTypeItem())->paginatedCollection(
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

        return response()->json(
            array_map(
                function($item) {
                    return (new ResourceTypeItemTransformer($item))->toArray();
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
     * @param Request $request
     * @param string $resource_type_id
     *
     * @return JsonResponse
     */
    public function optionsIndex(Request $request, string $resource_type_id): JsonResponse
    {
        $authenticated = Route::resourceType(
            $resource_type_id,
            $this->permitted_resource_types
        );

        $this->conditionalGetParameters(
            $resource_type_id,
            Parameters::fetch([
                'category',
            ])
        );

        $get = Get::init()->
            setSortable('api.resource-type-item.sortable')->
            setSearchable('api.resource-type-item.searchable')->
            setPagination(true)->
            setParameters('api.resource-type-item.parameters.collection')->
            setConditionalParameters($this->conditional_get_parameters)->
            setDescription('route-descriptions.resource_type_item_GET_index')->
            setAuthenticationStatus($authenticated)->
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
            $this->permitted_resource_types,
            $this->include_public,
            0,
            100,
            ['resource_type'=>$resource_type_id]
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
                        'description' => trans('item/allowed-values.description-prefix-subcategory') .
                            $subcategory['name'] . trans('item/allowed-values.description-suffix-subcategory')
                    ];
                },
                $subcategories
            );
          }
    }
}
