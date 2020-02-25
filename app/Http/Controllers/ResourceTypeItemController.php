<?php

namespace App\Http\Controllers;

use App\Item\Factory;
use App\Option\Get;
use App\Utilities\Header;
use App\Utilities\RoutePermission;
use App\Validators\Request\Parameters;
use App\Validators\Request\Route;
use App\Models\Category;
use App\Models\Subcategory;
use App\Utilities\Pagination as UtilityPagination;
use App\Validators\Request\SearchParameters;
use App\Validators\Request\SortParameters;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * View items for all resources for a resource type
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceTypeItemController extends Controller
{
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

        $item_interface = Factory::resourceTypeItem($resource_type_id);

        $resource_type_item_model = $item_interface->model();

        $collection_parameters = Parameters::fetch(
            array_keys($item_interface->collectionParameters()),
            $resource_type_id
        );

        $sort_fields = SortParameters::fetch(
            $item_interface->sortParameters()
        );

        $search_conditions = SearchParameters::fetch(
            $item_interface->searchParameters()
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
                function($item) use ($item_interface) {
                    return $item_interface->transformer($item)->toArray();
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

        $item_interface = Factory::resourceTypeItem($resource_type_id);

        $permissions = RoutePermission::resourceType(
            $resource_type_id,
            $this->permitted_resource_types
        );

        $defined_parameters = Parameters::fetch(
            array_keys($item_interface->collectionParameters()),
            $resource_type_id
        );

        $conditional_parameters = $this->conditionalGetParameters(
            $resource_type_id,
            array_merge(
                $item_interface->collectionParametersKeys(),
                $defined_parameters
            )
        );

        $get = Get::init()->
            setSortable($item_interface->sortParametersConfig())->
            setSearchable($item_interface->searchParametersConfig())->
            setPagination(true)->
            setParameters($item_interface->collectionParametersConfig())->
            setConditionalParameters($conditional_parameters)->
            setDescription('route-descriptions.resource_type_item_GET_index')->
            setAuthenticationStatus($permissions['view'])->
            option();

        return $this->optionsResponse($get, 200);
    }

    /**
     * Fetch the conditional GET parameters allowed values, ready to be be
     * merged with the GET parameters data array, useful for defining dynamic
     * values such as allowed values
     *
     * @param integer $resource_type_id
     * @param array $parameters
     *
     * @return array
     */
    private function conditionalGetParameters(
        $resource_type_id,
        array $parameters
    ): array
    {
        $item_interface = Factory::resourceTypeItem($resource_type_id);

        $conditional_parameters = [];

        if (array_key_exists('year', $parameters) === true) {
            $conditional_parameters['year']['allowed_values'] = [];

            for (
                $i = $item_interface->conditionalParameterMinYear($resource_type_id);
                $i <= $item_interface->conditionalParameterMaxYear($resource_type_id);
                $i++
            ) {
                $conditional_parameters['year']['allowed_values'][$i] = [
                    'value' => $i,
                    'name' => $i,
                    'description' => trans('resource-type-item-type-' . $item_interface->type() .
                            '/allowed-values.description-prefix-year') . $i
                ];
            }
        }

        if (array_key_exists('month', $parameters) === true) {
            $conditional_parameters['month']['allowed_values'] = [];

            for ($i=1; $i < 13; $i++) {
                $conditional_parameters['month']['allowed_values'][$i] = [
                    'value' => $i,
                    'name' => date("F", mktime(0, 0, 0, $i, 10)),
                    'description' => trans('resource-type-item-type-' . $item_interface->type() .
                        '/allowed-values.description-prefix-month') .
                        date("F", mktime(0, 0, 0, $i, 1))
                ];
            }
        }

        if (array_key_exists('category', $parameters) === true) {
            $conditional_parameters['category']['allowed_values'] = [];

            $categories = (new Category())->paginatedCollection(
                (int) $resource_type_id,
                $this->permitted_resource_types,
                $this->include_public,
                0,
                100
            );

            foreach ($categories as $category) {
                $conditional_parameters['category']['allowed_values'][$this->hash->encode('category', $category['category_id'])] = [
                    'value' => $this->hash->encode('category', $category['category_id']),
                    'name' => $category['category_name'],
                    'description' => trans('resource-type-item-type-' . $item_interface->type() .
                            '/allowed-values.description-prefix-category') .
                        $category['category_name'] .
                        trans('resource-type-item-type-' . $item_interface->type() .
                            '/allowed-values.description-suffix-category')
                ];
            }
        }

        if (
            array_key_exists('category', $parameters) === true &&
            $parameters['category'] !== null &&
            array_key_exists('subcategory', $parameters) === true
        ) {
            $conditional_parameters['subcategory']['allowed_values'] = [];

            $subcategories = (new Subcategory())->paginatedCollection(
                $resource_type_id,
                $parameters['category']
            );

            array_map(
                function($subcategory) use (&$conditional_parameters, $item_interface) {
                    $conditional_parameters['subcategory']['allowed_values'][$this->hash->encode('subcategory', $subcategory['subcategory_id'])] = [
                        'value' => $this->hash->encode('subcategory', $subcategory['subcategory_id']),
                        'name' => $subcategory['subcategory_name'],
                        'description' => trans('resource-type-item-type-' . $item_interface->type() . '/allowed-values.description-prefix-subcategory') .
                            $subcategory['subcategory_name'] . trans('resource-type-item-type-' . $item_interface->type() . '/allowed-values.description-suffix-subcategory')
                    ];
                },
                $subcategories
            );
        }

        return $conditional_parameters;
    }
}
