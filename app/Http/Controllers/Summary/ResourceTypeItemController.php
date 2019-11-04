<?php

namespace App\Http\Controllers\Summary;

use App\Http\Controllers\Controller;
use App\Option\Get;
use App\Utilities\Header;
use App\Utilities\RoutePermission;
use App\Validators\Request\Parameters;
use App\Validators\Request\Route;
use App\Models\ResourceTypeItem;
use App\Models\Transformers\Summary\ResourceTypeItemCategory as ResourceTypeItemCategoryTransformer;
use App\Models\Transformers\Summary\ResourceTypeItemMonth as ResourceTypeItemMonthTransformer;
use App\Models\Transformers\Summary\ResourceTypeItemResource as ResourceTypeItemResourceTransformer;
use App\Models\Transformers\Summary\ResourceTypeItemSubcategory as ResourceTypeItemSubcategoryTransformer;
use App\Models\Transformers\Summary\ResourceTypeItemYear as ResourceTypeItemYearTransformer;
use App\Utilities\General;
use App\Utilities\Response as UtilityResponse;
use App\Validators\Request\SearchParameters;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

/**
 * Summary for resource type items route
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceTypeItemController extends Controller
{
    private $resource_type_id;
    private $include_unpublished = false;

    /**
     * Return the TCO for all the resources within the resource type
     *
     * @param string $resource_type_id
     *
     * @return JsonResponse
     */
    public function index(string $resource_type_id): JsonResponse
    {
        Route::resourceType(
            $resource_type_id,
            $this->permitted_resource_types
        );

        $this->resource_type_id = $resource_type_id;

        $collection_parameters = Parameters::fetch(
            array_keys(Config::get('api.resource-type-item.summary-parameters.collection')),
            $resource_type_id
        );

        $search_parameters = SearchParameters::fetch([
            'description'
        ]);

        if (
            array_key_exists('include-unpublished', $collection_parameters) === true &&
            General::booleanValue($collection_parameters['include-unpublished']) === true
        ) {
            $this->include_unpublished = true;
        }

        if (array_key_exists('years', $collection_parameters) === true &&
            General::booleanValue($collection_parameters['years']) === true) {
            return $this->yearsSummary();
        } else if (
            array_key_exists('year', $collection_parameters) === true &&
            array_key_exists('category', $collection_parameters) === false &&
            array_key_exists('subcategory', $collection_parameters) === false &&
            count($search_parameters) === 0
        ) {
            if (
                array_key_exists('months', $collection_parameters) === true &&
                General::booleanValue($collection_parameters['months']) === true
            ) {
                return $this->monthsSummary($collection_parameters['year']);
            } else {
                if (array_key_exists('month', $collection_parameters) === true) {
                    return $this->monthSummary(
                        $collection_parameters['year'],
                        $collection_parameters['month']
                    );
                } else {
                    return $this->yearSummary($collection_parameters['year']);
                }
            }
        }

        if (array_key_exists('categories', $collection_parameters) === true &&
            General::booleanValue($collection_parameters['categories']) === true) {
            return $this->categoriesSummary();
        } else if (
            array_key_exists('category', $collection_parameters) === true &&
            array_key_exists('year', $collection_parameters) === false &&
            array_key_exists('month', $collection_parameters) === false &&
            count($search_parameters) === 0
        ) {
            if (
                array_key_exists('subcategories', $collection_parameters) === true &&
                General::booleanValue($collection_parameters['subcategories']) === true
            ) {
                return $this->subcategoriesSummary($collection_parameters['category']);
            } else {
                if (array_key_exists('subcategory', $collection_parameters) === true) {
                    return $this->subcategorySummary(
                        $collection_parameters['category'],
                        $collection_parameters['subcategory']
                    );
                } else {
                    return $this->categorySummary($collection_parameters['category']);
                }
            }
        }

        if (array_key_exists('resources', $collection_parameters) === true &&
            General::booleanValue($collection_parameters['resources']) === true) {
            return $this->resourcesSummary();
        }

        if (
            array_key_exists('category', $collection_parameters) === true ||
            array_key_exists('subcategory', $collection_parameters) === true ||
            array_key_exists('year', $collection_parameters) === true ||
            array_key_exists('month', $collection_parameters) === true ||
            count($search_parameters) > 0
        ) {
            return $this->filteredSummary(
                (array_key_exists('category', $collection_parameters) ? $collection_parameters['category'] : null),
                (array_key_exists('subcategory', $collection_parameters) ? $collection_parameters['subcategory'] : null),
                (array_key_exists('year', $collection_parameters) ? $collection_parameters['year'] : null),
                (array_key_exists('month', $collection_parameters) ? $collection_parameters['month'] : null),
                (count($search_parameters) > 0 ? $search_parameters : [])
            );
        }

        return $this->summary();
    }

    /**
     * Return the total summary for all the resources in the resource type
     *
     * @return JsonResponse
     */
    private function summary(): JsonResponse
    {
        $summary = (new \App\Models\ResourceTypeItemType\AllocatedExpense())->summary(
            $this->resource_type_id,
            $this->include_unpublished
        );

        if (count($summary) === 0) {
            UtilityResponse::successEmptyContent(true);
        }

        $headers = new Header();
        $headers->add('X-Total-Count', count($summary));
        $headers->add('X-Count', count($summary));

        $parameters_header = Parameters::xHeader();
        if ($parameters_header !== null) {
            $headers->addParameters($parameters_header);
        }

        return response()->json(
            [
                'total' => number_format(
                    $summary[0]['total'],
                    2,
                    '.',
                    ''
                )
            ],
            200,
            $headers->headers()
        );
    }

    /**
     * Return the total summary for all the resources in the resource type
     * grouped by resource
     *
     * @param int $resource_type_id
     * @param array $parameters
     *
     * @return JsonResponse
     */
    private function resourcesSummary(
        int $resource_type_id,
        array $parameters
    ): JsonResponse
    {
        $summary = (new ResourceTypeItem())->resourcesSummary(
            $resource_type_id,
            $parameters
        );

        if (count($summary) === 0) {
            UtilityResponse::successEmptyContent(true);
        }

        $headers = new Header();
        $headers->add('X-Total-Count', count($summary));
        $headers->add('X-Count', count($summary));

        $parameters_header = Parameters::xHeader();
        if ($parameters_header !== null) {
            $headers->addParameters($parameters_header);
        }

        return response()->json(
            array_map(
                function ($resource) {
                    return (new ResourceTypeItemResourceTransformer($resource))->toArray();
                },
                $summary
            ),
            200,
            $headers->headers()
        );
    }

    /**
     * Return the total summary for all the resources in the resource type
     * grouped by year
     *
     * @param int $resource_type_id
     * @param array $parameters
     *
     * @return JsonResponse
     */
    private function yearsSummary(
        int $resource_type_id,
        array $parameters
    ): JsonResponse
    {
        $summary = (new ResourceTypeItem())->yearsSummary(
            $resource_type_id,
            $parameters
        );

        if (count($summary) === 0) {
            UtilityResponse::successEmptyContent(true);
        }

        $headers = new Header();
        $headers->add('X-Total-Count', count($summary));
        $headers->add('X-Count', count($summary));

        $parameters_header = Parameters::xHeader();
        if ($parameters_header !== null) {
            $headers->addParameters($parameters_header);
        }

        return response()->json(
            array_map(
                function ($year) {
                    return (new ResourceTypeItemYearTransformer($year))->toArray();
                },
                $summary
            ),
            200,
            $headers->headers()
        );
    }

    /**
     * Return the total summary for all the resources in the resource type
     * for the requested year
     *
     * @param int $resource_type_id
     * @param int $year
     * @param array $parameters
     *
     * @return JsonResponse
     */
    private function yearSummary(
        int $resource_type_id,
        int $year,
        array $parameters
    ): JsonResponse
    {
        $summary = (new ResourceTypeItem())->yearSummary(
            $resource_type_id,
            $year,
            $parameters
        );

        if (count($summary) === 0) {
            UtilityResponse::successEmptyContent();
        }

        $headers = new Header();
        $headers->add('X-Total-Count', count($summary));
        $headers->add('X-Count', count($summary));

        $parameters_header = Parameters::xHeader();
        if ($parameters_header !== null) {
            $headers->addParameters($parameters_header);
        }

        return response()->json(
            (new ResourceTypeItemYearTransformer($summary[0]))->toArray(),
            200,
            $headers->headers()
        );
    }

    /**
     * Return the total summary for all the resources in the resource type
     * grouped by year
     *
     * @param int $resource_type_id
     * @param int $year
     * @param array $parameters
     *
     * @return JsonResponse
     */
    private function monthsSummary(
        int $resource_type_id,
        int $year,
        array $parameters
    ): JsonResponse
    {
        $summary = (new ResourceTypeItem())->monthsSummary(
            $resource_type_id,
            $year,
            $parameters
        );

        if (count($summary) === 0) {
            UtilityResponse::successEmptyContent(true);
        }

        $headers = new Header();
        $headers->add('X-Total-Count', count($summary));
        $headers->add('X-Count', count($summary));

        $parameters_header = Parameters::xHeader();
        if ($parameters_header !== null) {
            $headers->addParameters($parameters_header);
        }

        return response()->json(
            array_map(
                function ($month) {
                    return (new ResourceTypeItemMonthTransformer($month))->toArray();
                },
                $summary
            ),
            200,
            $headers->headers()
        );
    }

    /**
     * Return the total summary for all the resources in the resource type
     * for a specific month
     *
     * @param int $resource_type_id
     * @param int $year
     * @param int $month
     * @param array $parameters
     *
     * @return JsonResponse
     */
    private function monthSummary(
        int $resource_type_id,
        int $year,
        int $month,
        array $parameters
    ): JsonResponse
    {
        $summary = (new ResourceTypeItem())->monthSummary(
            $resource_type_id,
            $year,
            $month,
            $parameters
        );

        if (count($summary) === 0) {
            UtilityResponse::successEmptyContent();
        }

        $headers = new Header();
        $headers->add('X-Total-Count', count($summary));
        $headers->add('X-Count', count($summary));

        $parameters_header = Parameters::xHeader();
        if ($parameters_header !== null) {
            $headers->addParameters($parameters_header);
        }

        return response()->json(
            (new ResourceTypeItemMonthTransformer($summary[0]))->toArray(),
            200,
            $headers->headers()
        );
    }

    /**
     * Return the total summary for all the resources in the resource type
     * grouped by category
     *
     * @param int $resource_type_id
     * @param array $parameters
     *
     * @return JsonResponse
     */
    private function categoriesSummary(
        int $resource_type_id,
        array $parameters
    ): JsonResponse
    {
        $summary = (new ResourceTypeItem())->categoriesSummary(
            $resource_type_id,
            $parameters
        );

        if (count($summary) === 0) {
            UtilityResponse::successEmptyContent(true);
        }

        $headers = new Header();
        $headers->add('X-Total-Count', count($summary));
        $headers->add('X-Count', count($summary));

        $parameters_header = Parameters::xHeader();
        if ($parameters_header !== null) {
            $headers->addParameters($parameters_header);
        }

        return response()->json(
            array_map(
                function ($category) {
                    return (new ResourceTypeItemCategoryTransformer($category))->toArray();
                },
                $summary
            ),
            200,
            $headers->headers()
        );
    }

    /**
     * Return the total summary for all the resources in the resource type
     * for a specific category
     *
     * @param int $resource_type_id
     * @param int $category_id
     * @param array $parameters
     *
     * @return JsonResponse
     */
    private function categorySummary(
        int $resource_type_id,
        int $category_id,
        array $parameters
    ): JsonResponse
    {
        $summary = (new ResourceTypeItem())->categorySummary(
            $resource_type_id,
            $category_id,
            $parameters
        );

        if (count($summary) === 0) {
            UtilityResponse::successEmptyContent();
        }

        $headers = new Header();
        $headers->add('X-Total-Count', count($summary));
        $headers->add('X-Count', count($summary));

        $parameters_header = Parameters::xHeader();
        if ($parameters_header !== null) {
            $headers->addParameters($parameters_header);
        }

        return response()->json(
            (new ResourceTypeItemCategoryTransformer($summary[0]))->toArray(),
            200,
            $headers->headers()
        );
    }

    /**
     * Return a filtered summary
     *
     * @param int $resource_type_id
     * @param int|null $category_id
     * @param int|null $subcategory_id
     * @param int|null $year
     * @param int|null $month
     * @param array $parameters
     * @param array $search_parameters
     *
     * @return JsonResponse
     */
    protected function filteredSummary(
        int $resource_type_id,
        int $category_id = null,
        int $subcategory_id = null,
        int $year = null,
        int $month = null,
        array $parameters = [],
        array $search_parameters = []
    ): JsonResponse
    {
        $summary = (new ResourceTypeItem())->filteredSummary(
            $resource_type_id,
            $category_id,
            $subcategory_id,
            $year,
            $month,
            $search_parameters,
            $parameters
        );

        if (count($summary) === 0) {
            UtilityResponse::successEmptyContent();
        }

        $headers = new Header();
        $headers->add('X-Total-Count', count($summary));
        $headers->add('X-Count', count($summary));

        $parameters_header = Parameters::xHeader();
        if ($parameters_header !== null) {
            $headers->addParameters($parameters_header);
        }

        return response()->json(
            [
                'total' => number_format($summary[0]['total'], 2, '.', '')
            ],
            200,
            $headers->headers()
        );
    }

    /**
     * Return the total summary for all the resources in the resource type
     * and category grouped by subcategory
     *
     * @param int $resource_type_id
     * @param int $category_id
     * @param array $parameters
     *
     * @return JsonResponse
     */
    private function subcategoriesSummary(
        int $resource_type_id,
        int $category_id,
        array $parameters
    ): JsonResponse
    {
        $summary = (new ResourceTypeItem())->subcategoriesSummary(
            $resource_type_id,
            $category_id,
            $parameters
        );

        if (count($summary) === 0) {
            UtilityResponse::successEmptyContent(true);
        }

        $headers = new Header();
        $headers->add('X-Total-Count', count($summary));
        $headers->add('X-Count', count($summary));

        $parameters_header = Parameters::xHeader();
        if ($parameters_header !== null) {
            $headers->addParameters($parameters_header);
        }

        return response()->json(
            array_map(
                function ($category) {
                    return (new ResourceTypeItemSubcategoryTransformer($category))->toArray();
                },
                $summary
            ),
            200,
            $headers->headers()
        );
    }

    /**
     * Return the total summary for all the resources in the resource type
     * for a specific category and subcategory
     *
     * @param int $resource_type_id
     * @param int $category_id
     * @param int $subcategory_id
     * @param array $parameters
     *
     * @return JsonResponse
     */
    private function subcategorySummary(
        int $resource_type_id,
        int $category_id,
        int $subcategory_id,
        array $parameters
    ): JsonResponse
    {
        $summary = (new ResourceTypeItem())->subcategorySummary(
            $resource_type_id,
            $category_id,
            $subcategory_id,
            $parameters
        );

        if (count($summary) === 0) {
            UtilityResponse::successEmptyContent();
        }

        $headers = new Header();
        $headers->add('X-Total-Count', count($summary));
        $headers->add('X-Count', count($summary));

        $parameters_header = Parameters::xHeader();
        if ($parameters_header !== null) {
            $headers->addParameters($parameters_header);
        }

        return response()->json(
            (new ResourceTypeItemSubcategoryTransformer($summary[0]))->toArray(),
            200,
            $headers->headers()
        );
    }


    /**
     * Generate the OPTIONS request for items summary route
     *
     * @param string $resource_type_id
     *
     * @return JsonResponse
     *
     */
    public function optionsIndex(string $resource_type_id): JsonResponse
    {
        Route::resourceType(
            (int) $resource_type_id,
            $this->permitted_resource_types
        );

        $permissions = RoutePermission::resourceType(
            $resource_type_id,
            $this->permitted_resource_types
        );

        $get = Get::init()->
            setSearchable('api.resource-type-item.searchable')->
            setParameters('api.resource-type-item.summary-parameters.collection')->
            setDescription('route-descriptions.summary-resource-type-item-GET-index')->
            setAuthenticationStatus($permissions['view'])->
            option();

        return $this->optionsResponse($get, 200);
    }
}
