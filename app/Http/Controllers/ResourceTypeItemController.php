<?php

namespace App\Http\Controllers;

use App\ResourceTypeItem\Factory;
use App\Option\Get;
use App\Response\Cache;
use App\Response\Header\Header;
use App\Request\Parameter;
use App\Request\Route;
use App\Models\Category;
use App\Models\Subcategory;
use App\Response\Header\Headers;
use App\Utilities\Pagination as UtilityPagination;
use Illuminate\Http\JsonResponse;

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
     * @param string $resource_type_id
     *
     * @return JsonResponse
     */
    public function index(string $resource_type_id): JsonResponse
    {
        Route\Validate::resourceType(
            $resource_type_id,
            $this->permitted_resource_types
        );

        $cache_control = new Cache\Control($this->user_id);
        $cache_control->setTtlOneDay();

        $item_interface = Factory::item($resource_type_id);

        $resource_type_item_model = $item_interface->model();

        $collection_parameters = Parameter\Request::fetch(
            array_keys($item_interface->collectionParameters()),
            $resource_type_id
        );

        $sort_fields = Parameter\Sort::fetch(
            $item_interface->sortParameters()
        );

        $search_parameters = Parameter\Search::fetch(
            $item_interface->searchParameters()
        );

        $filter_parameters = Parameter\Filter::fetch(
            $item_interface->filterParameters()
        );

        $cache_collection = new Cache\Collection();
        $cache_collection->setFromCache($cache_control->get(request()->getRequestUri()));

        if ($cache_collection->valid() === false) {

            $total = $resource_type_item_model->totalCount(
                $resource_type_id,
                $collection_parameters,
                $search_parameters,
                $filter_parameters
            );

            $pagination = UtilityPagination::init(request()->path(), $total)
                ->setParameters()
                ->paging();

            $items = $resource_type_item_model->paginatedCollection(
                $resource_type_id,
                $pagination['offset'],
                $pagination['limit'],
                $collection_parameters,
                $search_parameters,
                $filter_parameters,
                $sort_fields
            );

            $collection = array_map(
                static function ($item) use ($item_interface) {
                    return $item_interface->transformer($item)->toArray();
                },
                $items
            );

            $headers = new Headers();
            $headers->collection($pagination, count($items), $total)->
                addCacheControl($cache_control->visibility(), $cache_control->ttl())->
                addSearch(Parameter\Search::xHeader())->
                addSort(Parameter\Sort::xHeader())->
                addParameters(Parameter\Request::xHeader())->
                addFilters(Parameter\Filter::xHeader());

            $cache_collection->create($total, $collection, $pagination, $headers->headers());
            $cache_control->put(request()->getRequestUri(), $cache_collection->content());
        }

        return response()->json($cache_collection->collection(), 200, $cache_collection->headers());
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
        Route\Validate::resourceType(
            $resource_type_id,
            $this->permitted_resource_types
        );

        $item_interface = Factory::item($resource_type_id);

        $permissions = Route\Permission::resourceType(
            $resource_type_id,
            $this->permitted_resource_types
        );

        $defined_parameters = Parameter\Request::fetch(
            array_keys($item_interface->collectionParameters()),
            $resource_type_id
        );

        $parameters_data = $this->parametersData(
            $resource_type_id,
            array_merge(
                $item_interface->collectionParametersKeys(),
                $defined_parameters
            )
        );

        $get = Get::init()->
            setSortable($item_interface->sortParametersConfig())->
            setSearchable($item_interface->searchParametersConfig())->
            setFilterable($item_interface->filterParametersConfig())->
            setPagination(true)->
            setParameters($item_interface->collectionParametersConfig())->
            setParametersData($parameters_data)->
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
    private function parametersData(
        $resource_type_id,
        array $parameters
    ): array
    {
        $item_interface = Factory::item($resource_type_id);

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
