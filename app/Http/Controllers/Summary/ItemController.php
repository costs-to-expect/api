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
    private $model;

    /**
     * Return the TCO for the resource or pass the request off to the relevant
     * method
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

        $item_interface = ItemInterfaceFactory::summaryItem($resource_type_id);
        $this->model = $item_interface->model();

        $collection_parameters = Parameters::fetch(
            $item_interface->collectionParametersKeys(),
            (int) $resource_type_id,
            (int) $resource_id
        );

        $search_parameters = SearchParameters::fetch(
            $item_interface->searchParameters()
        );

        if (array_key_exists('years', $collection_parameters) === true &&
            General::booleanValue($collection_parameters['years']) === true) {
            return $this->yearsSummary(
                (int) $resource_type_id,
                (int) $resource_id,
                $collection_parameters
            );
        } else if (
            array_key_exists('year', $collection_parameters) === true &&
            array_key_exists('category', $collection_parameters) === false &&
            array_key_exists('subcategory', $collection_parameters) === false &&
            count($search_parameters) === 0
        ) {
            if (array_key_exists('months', $collection_parameters) === true &&
                General::booleanValue($collection_parameters['months']) === true) {
                return $this->monthsSummary(
                    (int) $resource_type_id,
                    (int) $resource_id,
                    (int) $collection_parameters['year'],
                    $collection_parameters
                );
            } else if (array_key_exists('month', $collection_parameters) === true) {
                return $this->monthSummary(
                    (int) $resource_type_id,
                    (int) $resource_id,
                    (int) $collection_parameters['year'],
                    (int) $collection_parameters['month'],
                    $collection_parameters
                );
            } else {
                return $this->yearSummary(
                    (int) $resource_type_id,
                    (int) $resource_id,
                    (int) $collection_parameters['year'],
                    $collection_parameters
                );
            }
        }

        if (array_key_exists('categories', $collection_parameters) === true &&
            General::booleanValue($collection_parameters['categories']) === true) {
            return $this->categoriesSummary(
                (int) $resource_type_id,
                (int) $resource_id,
                $collection_parameters
            );
        } else if (
            array_key_exists('category', $collection_parameters) === true &&
            array_key_exists('year', $collection_parameters) === false &&
            array_key_exists('month', $collection_parameters) === false &&
            count($search_parameters) === 0
        ) {
            if (array_key_exists('subcategories', $collection_parameters) === true &&
                General::booleanValue($collection_parameters['subcategories']) === true) {
                return $this->subcategoriesSummary(
                    (int) $resource_type_id,
                    (int) $resource_id,
                    (int) $collection_parameters['category'],
                    $collection_parameters
                );
            } else if (array_key_exists('subcategory', $collection_parameters) === true) {
                return $this->subcategorySummary(
                    (int) $resource_type_id,
                    (int) $resource_id,
                    (int) $collection_parameters['category'],
                    (int) $collection_parameters['subcategory'],
                    $collection_parameters
                );
            } else {
                return $this->categorySummary(
                    (int) $resource_type_id,
                    (int) $resource_id,
                    (int) $collection_parameters['category'],
                    $collection_parameters
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
                (int) $resource_type_id,
                (int) $resource_id,
                (array_key_exists('category', $collection_parameters) ? $collection_parameters['category'] : null),
                (array_key_exists('subcategory', $collection_parameters) ? $collection_parameters['subcategory'] : null),
                (array_key_exists('year', $collection_parameters) ? $collection_parameters['year'] : null),
                (array_key_exists('month', $collection_parameters) ? $collection_parameters['month'] : null),
                $collection_parameters,
                (count($search_parameters) > 0 ? $search_parameters : [])
            );
        }

        return $this->tcoSummary(
            (int) $resource_type_id,
            (int) $resource_id,
            $collection_parameters
        );
    }

    /**
     * Return the total summary for a resource, total cost of ownership
     *
     * @param int $resource_type_id
     * @param int $resource_id
     * @param array $parameters
     *
     * @return JsonResponse
     */
    private function tcoSummary(
        int $resource_type_id,
        int $resource_id,
        array $parameters
    ): JsonResponse
    {
        $summary = (new Item())->summary(
            $resource_type_id,
            $resource_id,
            $parameters
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
                'total' => number_format($summary[0]['total'], 2, '.', '')
            ],
            200,
            $headers->headers()
        );
    }

    /**
     * Return the annualised summary for a resource
     *
     * @param int $resource_type_id
     * @param int $resource_id
     * @param array $parameters
     *
     * @return JsonResponse
     */
    private function yearsSummary(
        int $resource_type_id,
        int $resource_id,
        array $parameters
    ): JsonResponse
    {
        $summary = (new Item())->yearsSummary(
            $resource_type_id,
            $resource_id,
            $parameters
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
     * @param int $resource_type_id,
     * @param int $resource_id
     * @param int $year
     * @param array $parameters
     *
     * @return JsonResponse
     */
    private function yearSummary(
        int $resource_type_id,
        int $resource_id,
        int $year,
        array $parameters
    ): JsonResponse
    {
        $summary = (new Item())->yearSummary(
            $resource_type_id,
            $resource_id,
            $year,
            $parameters
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
     * @param int $resource_type_id
     * @param int $resource_id
     * @param int $year
     * @param array $parameters
     *
     * @return JsonResponse
     */
    private function monthsSummary(
        int $resource_type_id,
        int $resource_id,
        int $year,
        array $parameters
    ): JsonResponse
    {
        $summary = (new Item())->monthsSummary(
            $resource_type_id,
            $resource_id,
            $year,
            $parameters
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
     * @param int $resource_type_id
     * @param int $resource_id
     * @param int $year
     * @param int $month
     * @param array $parameters
     *
     * @return JsonResponse
     */
    private function monthSummary(
        int $resource_type_id,
        int $resource_id,
        int $year,
        int $month,
        array $parameters
    ): JsonResponse
    {
        $summary = (new Item())->monthSummary(
            $resource_type_id,
            $resource_id,
            $year,
            $month,
            $parameters
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
     * @param int $resource_type_id
     * @param int $resource_id
     * @param array $parameters
     *
     * @return JsonResponse
     */
    private function categoriesSummary(
        int $resource_type_id,
        int $resource_id,
        array $parameters
    ): JsonResponse
    {
        $summary = (new Item())->categoriesSummary(
            $resource_type_id,
            $resource_id,
            $parameters
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
     * @param int $resource_type_id
     * @param int $resource_id
     * @param int|null $category_id
     * @param int|null $subcategory_id
     * @param int|null $year
     * @param int|null $month
     * @param array $parameters
     * @param array $search_parameters
     *
     * @return JsonResponse
     */
    private function filteredSummary(
        int $resource_type_id,
        int $resource_id,
        int $category_id = null,
        int $subcategory_id = null,
        int $year = null,
        int $month = null,
        array $parameters = [],
        array $search_parameters = []
    ): JsonResponse
    {
        $summary = (new Item())->filteredSummary(
            $resource_type_id,
            $resource_id,
            $category_id,
            $subcategory_id,
            $year,
            $month,
            $parameters,
            $search_parameters,
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
     * @param int $resource_type_id
     * @param int $resource_id
     * @param int $category_id
     * @param array $parameters
     *
     * @return JsonResponse
     */
    private function categorySummary(
        int $resource_type_id,
        int $resource_id,
        int $category_id,
        array $parameters
    ): JsonResponse
    {
         $summary = (new Item())->categorySummary(
            $resource_type_id,
            $resource_id,
            $category_id,
            $parameters
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
     * @param int $resource_type_id
     * @param int $resource_id
     * @param int $category_id
     * @param array $parameters
     *
     * @return JsonResponse
     */
    private function subcategoriesSummary(
        int $resource_type_id,
        int $resource_id,
        int $category_id,
        array $parameters
    ): JsonResponse
    {
        $summary = (new Item())->subCategoriesSummary(
            $resource_type_id,
            $resource_id,
            $category_id,
            $parameters
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
     * @param int $resource_type_id
     * @param int $resource_id
     * @param int $category_id
     * @param int $subcategory_id
     * @param array $parameters
     *
     * @return JsonResponse
     */
    private function subcategorySummary(
        int $resource_type_id,
        int $resource_id,
        int $category_id,
        int $subcategory_id,
        array $parameters
    ): JsonResponse
    {
        $summary = (new Item())->subCategorySummary(
            $resource_type_id,
            $resource_id,
            $category_id,
            $subcategory_id,
            $parameters
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

        $item_interface = ItemInterfaceFactory::summaryItem($resource_type_id);

        $permissions = RoutePermission::resource(
            $resource_type_id,
            $resource_id,
            $this->permitted_resource_types
        );

        $get = Get::init()->
            setParameters($item_interface->collectionParametersConfig())->
            setSearchable($item_interface->searchParametersConfig())->
            setDescription('route-descriptions.summary_GET_resource-type_resource_items')->
            setAuthenticationStatus($permissions['view'])->
            option();

        return $this->optionsResponse($get, 200);
    }
}
