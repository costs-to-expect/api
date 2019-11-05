<?php

namespace App\Http\Controllers\Summary;

use App\Http\Controllers\Controller;
use App\Option\Get;
use App\Utilities\Header;
use App\Utilities\RoutePermission;
use App\Validators\Request\Parameters;
use App\Validators\Request\Route;
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
    private $model;

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

        /*$item_interface = ItemInterfaceFactory::summaryItem($resource_type_id);
        $this->model = $item_interface->model();*/

        $this->model = new \App\Models\ResourceTypeItemType\Summary\AllocatedExpense();

        $parameters = Parameters::fetch(
            array_keys(Config::get('api.resource-type-item.summary-parameters.collection')),
            $resource_type_id
        );

        $resources = false;
        $years = false;
        $months = false;
        $categories = false;
        $subcategories = false;
        $year = null;
        $month = null;
        $category = null;
        $subcategory = null;

        if (
            array_key_exists('resources', $parameters) === true &&
            General::booleanValue($parameters['resources']) === true
        ) {
            $resources = true;
        }

        if (
            array_key_exists('years', $parameters) === true &&
            General::booleanValue($parameters['years']) === true
        ) {
            $years = true;
        }

        if (
            array_key_exists('months', $parameters) === true &&
            General::booleanValue($parameters['months']) === true
        ) {
            $months = true;
        }

        if (array_key_exists('categories', $parameters) === true &&
            General::booleanValue($parameters['categories']) === true) {
            $categories = true;
        }

        if (array_key_exists('subcategories', $parameters) === true &&
                General::booleanValue($parameters['subcategories']) === true) {
            $subcategories = true;
        }

        if (array_key_exists('year', $parameters) === true) {
            $year = (int) $parameters['year'];
        }

        if (array_key_exists('month', $parameters) === true) {
            $month = (int) $parameters['month'];
        }

        if (array_key_exists('category', $parameters) === true) {
            $category = (int) $parameters['category'];
        }

        if (array_key_exists('subcategory', $parameters) === true) {
            $subcategory = (int) $parameters['subcategory'];
        }

        unset(
            $parameters['resources'],
            $parameters['years'],
            $parameters['year'],
            $parameters['months'],
            $parameters['month'],
            $parameters['categories'],
            $parameters['category'],
            $parameters['subcategories'],
            $parameters['subcategory']
        );

        $search_parameters = SearchParameters::fetch([
            'description'
        ]);

        if ($years === true) {
            return $this->yearsSummary(
                $resource_type_id,
                $parameters
            );
        } else if (
            $years === true &&
            $category === null &&
            $subcategory === null &&
            count($search_parameters) === 0
        ) {
            if ($months === true) {
                return $this->monthsSummary(
                    $resource_type_id,
                    $year,
                    $parameters
                );
            } else {
                if ($month !== null) {
                    return $this->monthSummary(
                        $resource_type_id,
                        $year,
                        $month,
                        $parameters
                    );
                } else {
                    return $this->yearSummary(
                        $resource_type_id,
                        $year,
                        $parameters
                    );
                }
            }
        }

        if ($categories === true) {
            return $this->categoriesSummary(
                $resource_type_id,
                $parameters
            );
        } else if (
            $category !== null &&
            $year === null &&
            $month === null &&
            count($search_parameters) === 0
        ) {
            if ($subcategories === true) {
                return $this->subcategoriesSummary(
                    $resource_type_id,
                    $category,
                    $parameters
                );
            } else {
                if ($subcategory !== null) {
                    return $this->subcategorySummary(
                        $resource_type_id,
                        $category,
                        $subcategory,
                        $parameters
                    );
                } else {
                    return $this->categorySummary(
                        $resource_type_id,
                        $category,
                        $parameters
                    );
                }
            }
        }

        if ($resources === true) {
            return $this->resourcesSummary(
                $resource_type_id,
                $parameters
            );
        }

        if (
            $category === true ||
            $subcategory === true ||
            $year === true ||
            $month === true ||
            count($search_parameters) > 0
        ) {
            return $this->filteredSummary(
                $resource_type_id,
                $category,
                $subcategory,
                $year,
                $month,
                $parameters,
                (count($search_parameters) > 0 ? $search_parameters : [])
            );
        }

        return $this->summary(
            $resource_type_id,
            $parameters
        );
    }

    /**
     * Return the total summary for all the resources in the resource type
     *
     * @param int $resource_type_id
     * @param array $parameters
     *
     * @return JsonResponse
     */
    private function summary(
        int $resource_type_id,
        array $parameters
    ): JsonResponse
    {
        $summary = $this->model->summary(
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
        $summary = $this->model->resourcesSummary(
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
        $summary = $this->model->yearsSummary(
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
        $summary = $this->model->yearSummary(
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
        $summary = $this->model->monthsSummary(
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
        $summary = $this->model->monthSummary(
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
        $summary = $this->model->categoriesSummary(
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
        $summary = $this->model->categorySummary(
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
        $summary = $this->model->filteredSummary(
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
        $summary = $this->model->subcategoriesSummary(
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
        $summary = $this->model->subcategorySummary(
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
