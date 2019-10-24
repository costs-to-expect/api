<?php

namespace App\Http\Controllers\Summary;

use App\Http\Controllers\Controller;
use App\Item\ItemInterfaceFactory;
use App\Option\Get;
use App\Utilities\Header;
use App\Utilities\Response;
use App\Utilities\RoutePermission;
use App\Validators\Request\Parameters;
use App\Validators\Request\Route;
use App\Models\Summary\Item;
use App\Models\Transformers\Summary\ItemCategory as ItemCategoryTransformer;
use App\Models\Transformers\Summary\ItemMonth as ItemMonthTransformer;
use App\Models\Transformers\Summary\ItemSubcategory as ItemSubcategoryTransformer;
use App\Models\Transformers\Summary\ItemYear as ItemYearTransformer;
use App\Utilities\General;
use App\Validators\Request\SearchParameters;
use Illuminate\Http\JsonResponse;

/**
 * Summary for the items route
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemController extends Controller
{
    private $resource_type_id;
    private $resource_id;
    private $include_unpublished = false;

    /**
     * Return the TCO for the resource
     *
     * @param string $resource_type_id
     * @param string $resource_id
     *
     * @return JsonResponse
     */
    public function index(string $resource_type_id, string $resource_id): JsonResponse
    {
        Route::resource(
            $resource_type_id,
            $resource_id,
            $this->permitted_resource_types
        );

        $this->resource_type_id = $resource_type_id;
        $this->resource_id = $resource_id;

        $collection_parameters = Parameters::fetch([
            'include-unpublished',
            'year',
            'years',
            'month',
            'months',
            'category',
            'categories',
            'subcategory',
            'subcategories'
        ]);

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
            if (array_key_exists('months', $collection_parameters) === true &&
                General::booleanValue($collection_parameters['months']) === true) {
                return $this->monthsSummary($collection_parameters['year']);
            } else if (array_key_exists('month', $collection_parameters) === true) {
                return $this->monthSummary(
                    $collection_parameters['year'],
                    $collection_parameters['month']
                );
            } else {
                return $this->yearSummary($collection_parameters['year']);
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
            if (array_key_exists('subcategories', $collection_parameters) === true &&
                General::booleanValue($collection_parameters['subcategories']) === true) {
                return $this->subcategoriesSummary(
                    $resource_type_id,
                    $collection_parameters['category']
                );
            } else if (array_key_exists('subcategory', $collection_parameters) === true) {
                return $this->subcategorySummary(
                    $resource_type_id,
                    $collection_parameters['category'],
                    $collection_parameters['subcategory']
                );
            } else {
                return $this->categorySummary(
                    $resource_type_id,
                    $collection_parameters['category']
                );
            }
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

        return $this->tcoSummary();
    }

    /**
     * Return the total summary for a resource, total cost of ownership
     *
     * @return JsonResponse
     */
    private function tcoSummary(): JsonResponse
    {
        $summary = (new Item())->summary(
            $this->resource_type_id,
            $this->resource_id,
            $this->include_unpublished
        );

        if (count($summary) === 0) {
            Response::successEmptyContent(true);
        }

        $headers = new Header();
        $headers->add('X-Total-Count', 1);
        $headers->add('X-Count', 1);

        $parameters_header = Parameters::xHeader();
        if ($parameters_header !== null) {
            $headers->addParameters($parameters_header);
        }

        return response()->json(
            [
                'total' => number_format($summary[0]['actualised_total'], 2, '.', '')
            ],
            200,
            $headers->headers()
        );
    }

    /**
     * Return the annualised summary for a resource
     *
     * @return JsonResponse
     */
    private function yearsSummary(): JsonResponse
    {
        $summary = (new Item())->yearsSummary(
            $this->resource_type_id,
            $this->resource_id,
            $this->include_unpublished
        );

        if (count($summary) === 0) {
            Response::successEmptyContent(true);
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
                function($year) {
                    return (new ItemYearTransformer($year))->toArray();
                },
                $summary
            ),
            200,
            $headers->headers()
        );
    }

    /**
     * Return the total cost for a specific year
     *
     * @param integer $year
     *
     * @return JsonResponse
     */
    private function yearSummary(int $year): JsonResponse
    {
        $summary = (new Item())->yearSummary(
            $this->resource_type_id,
            $this->resource_id,
            $year,
            $this->include_unpublished
        );

        if (count($summary) === 0) {
            Response::successEmptyContent();
        }

        $headers = new Header();
        $headers->add('X-Total-Count', count($summary));
        $headers->add('X-Count', count($summary));

        $parameters_header = Parameters::xHeader();
        if ($parameters_header !== null) {
            $headers->addParameters($parameters_header);
        }

        return response()->json(
            (new ItemYearTransformer($summary[0]))->toArray(),
            200,
            $headers->headers()
        );
    }

    /**
     * Return the monthly summary for a specific year
     *
     * @param integer $year
     *
     * @return JsonResponse
     */
    private function monthsSummary(int $year): JsonResponse
    {
        $summary = (new Item())->monthsSummary(
            $this->resource_type_id,
            $this->resource_id,
            $year,
            $this->include_unpublished
        );

        if (count($summary) === 0) {
            Response::successEmptyContent(true);
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
                function($month) {
                    return (new ItemMonthTransformer($month))->toArray();
                },
                $summary
            ),
            200,
            $headers->headers()
        );
    }

    /**
     * Return the month summary for a specific year and month
     *
     * @param integer $year
     * @param integer $month
     *
     * @return JsonResponse
     */
    private function monthSummary(int $year, int $month): JsonResponse
    {
        $summary = (new Item())->monthSummary(
            $this->resource_type_id,
            $this->resource_id,
            $year,
            $month,
            $this->include_unpublished
        );

        if (count($summary) === 0) {
            Response::successEmptyContent();
        }

        $headers = new Header();
        $headers->add('X-Total-Count', count($summary));
        $headers->add('X-Count', count($summary));

        $parameters_header = Parameters::xHeader();
        if ($parameters_header !== null) {
            $headers->addParameters($parameters_header);
        }

        return response()->json(
            (new ItemMonthTransformer($summary[0]))->toArray(),
            200,
            $headers->headers()
        );
    }

    /**
     * Return the categories summary for a resource
     *
     * @return JsonResponse
     */
    private function categoriesSummary(): JsonResponse
    {
        $summary = (new Item())->categoriesSummary(
            $this->resource_type_id,
            $this->resource_id,
            $this->include_unpublished
        );

        if (count($summary) === 0) {
            Response::successEmptyContent(true);
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
                function($category) {
                    return (new ItemCategoryTransformer($category))->toArray();
                },
                $summary
            ),
            200,
            $headers->headers()
        );
    }

    /**
     * Return a filtered summary
     *
     * @param int|null $category_id
     * @param int|null $subcategory_id
     * @param int|null $year
     * @param int|null $month
     * @param array $search_parameters
     *
     * @return JsonResponse
     */
    private function filteredSummary(
        int $category_id = null,
        int $subcategory_id = null,
        int $year = null,
        int $month = null,
        array $search_parameters = []
    ): JsonResponse
    {
        $summary = (new Item())->filteredSummary(
            $this->resource_type_id,
            $this->resource_id,
            $category_id,
            $subcategory_id,
            $year,
            $month,
            $search_parameters,
            $this->include_unpublished
        );

        if (count($summary) === 0) {
            Response::successEmptyContent(true);
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
     * Return the category summary for a resource
     *
     * @param integer $resource_type_id
     * @param integer $category_id
     *
     * @return JsonResponse
     */
    private function categorySummary(int $resource_type_id, int $category_id): JsonResponse
    {
        Route::category(
            $resource_type_id,
            $category_id,
            $this->permitted_resource_types
        );

        $summary = (new Item())->categorySummary(
            $this->resource_type_id,
            $this->resource_id,
            $category_id,
            $this->include_unpublished
        );

        if (count($summary) === 0) {
            Response::successEmptyContent();
        }

        $headers = new Header();
        $headers->add('X-Total-Count', count($summary));
        $headers->add('X-Count', count($summary));

        $parameters_header = Parameters::xHeader();
        if ($parameters_header !== null) {
            $headers->addParameters($parameters_header);
        }

        return response()->json(
            (new ItemCategoryTransformer($summary[0]))->toArray(),
            200,
            $headers->headers()
        );
    }

    /**
     * Return the subcategories summary for a category
     *
     * @param integer $resource_type_id
     * @param integer $category_id
     *
     * @return JsonResponse
     */
    private function subcategoriesSummary(int $resource_type_id, int $category_id): JsonResponse
    {
        Route::category(
            $resource_type_id,
            $category_id,
            $this->permitted_resource_types
        );

        $summary = (new Item())->subCategoriesSummary(
            $this->resource_type_id,
            $this->resource_id,
            $category_id,
            $this->include_unpublished
        );

        if (count($summary) === 0) {
            Response::successEmptyContent(true);
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
                function($subcategory) {
                    return (new ItemSubcategoryTransformer($subcategory))->toArray();
                },
                $summary
            ),
            200,
            $headers->headers()
        );
    }

    /**
     * Return the subcategories summary for a category
     *
     * @param integer $resource_type_id
     * @param integer $category_id
     * @param integer $subcategory_id
     *
     * @return JsonResponse
     */
    private function subcategorySummary(
        int $resource_type_id,
        int $category_id,
        int $subcategory_id
    ): JsonResponse
    {
        Route::subcategory(
            $resource_type_id,
            $category_id,
            $subcategory_id,
            $this->permitted_resource_types
        );

        $summary = (new Item())->subCategorySummary(
            $this->resource_type_id,
            $this->resource_id,
            $category_id,
            $subcategory_id,
            $this->include_unpublished
        );

        if (count($summary) === 0) {
            Response::successEmptyContent();
        }

        $headers = new Header();
        $headers->add('X-Total-Count', count($summary));
        $headers->add('X-Count', count($summary));

        $parameters_header = Parameters::xHeader();
        if ($parameters_header !== null) {
            $headers->addParameters($parameters_header);
        }

        return response()->json(
            (new ItemSubcategoryTransformer($summary[0]))->toArray(),
            200,
            $headers->headers()
        );
    }

    /**
     * Generate the OPTIONS request for items route
     *
     * @param string $resource_type_id
     * @param string $resource_id
     *
     * @return JsonResponse
     */
    public function optionsIndex(string $resource_type_id, string $resource_id)
    {
        Route::resource(
            $resource_type_id,
            $resource_id,
            $this->permitted_resource_types
        );

        $item_interface = ItemInterfaceFactory::item($resource_type_id);

        $permissions = RoutePermission::resource(
            $resource_type_id,
            $resource_id,
            $this->permitted_resource_types
        );

        $get = Get::init()->
            setParameters('api.item.summary-parameters.collection')->
            setSearchable($item_interface->searchParametersConfig())->
            setDescription('route-descriptions.summary_GET_resource-type_resource_items')->
            setAuthenticationStatus($permissions['view'])->
            option();

        return $this->optionsResponse($get, 200);
    }
}
