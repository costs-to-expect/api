<?php

namespace App\Http\Controllers\Summary;

use App\Http\Controllers\Controller;
use App\Item\Factory;
use App\Option\Get;
use App\Response\Cache;
use App\Request\Parameter;
use App\Request\Route;
use App\Models\Transformers\Summary\ItemCategory as ItemCategoryTransformer;
use App\Models\Transformers\Summary\ItemMonth as ItemMonthTransformer;
use App\Models\Transformers\Summary\ItemSubcategory as ItemSubcategoryTransformer;
use App\Models\Transformers\Summary\ItemYear as ItemYearTransformer;
use App\Response\Header\Headers;
use App\Utilities\General;
use Illuminate\Http\JsonResponse;

/**
 * Summary for the items route
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
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
        Route\Validate::resource(
            $resource_type_id,
            $resource_id,
            $this->permitted_resource_types
        );

        $item_interface = Factory::summaryItem($resource_type_id);
        $this->model = $item_interface->model();

        $parameters = Parameter\Request::fetch(
            $item_interface->collectionParametersKeys(),
            (int) $resource_type_id,
            (int) $resource_id
        );

        $years = false;
        $months = false;
        $categories = false;
        $subcategories = false;
        $year = null;
        $month = null;
        $category = null;
        $subcategory = null;

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
            $parameters['years'],
            $parameters['year'],
            $parameters['months'],
            $parameters['month'],
            $parameters['categories'],
            $parameters['category'],
            $parameters['subcategories'],
            $parameters['subcategory']
        );

        $search_parameters = Parameter\Search::fetch(
            $item_interface->searchParameters()
        );

        $filter_parameters = Parameter\Filter::fetch(
            $item_interface->filterParameters()
        );

        if ($years === true) {
            return $this->yearsSummary(
                (int) $resource_type_id,
                (int) $resource_id,
                $parameters
            );
        } else if (
            $year !== null &&
            $category === null &&
            $subcategory === null &&
            count($search_parameters) === 0
        ) {
            if ($months === true) {
                return $this->monthsSummary(
                    (int) $resource_type_id,
                    (int) $resource_id,
                    $year,
                    $parameters
                );
            } else if ($month !== null) {
                return $this->monthSummary(
                    (int) $resource_type_id,
                    (int) $resource_id,
                    $year,
                    $month,
                    $parameters
                );
            } else {
                return $this->yearSummary(
                    (int) $resource_type_id,
                    (int) $resource_id,
                    $year,
                    $parameters
                );
            }
        }

        if ($categories === true) {
            return $this->categoriesSummary(
                (int) $resource_type_id,
                (int) $resource_id,
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
                    (int) $resource_type_id,
                    (int) $resource_id,
                    $category,
                    $parameters
                );
            } else if ($subcategory !== null) {
                return $this->subcategorySummary(
                    (int) $resource_type_id,
                    (int) $resource_id,
                    $category,
                    $subcategory,
                    $parameters
                );
            } else {
                return $this->categorySummary(
                    (int) $resource_type_id,
                    (int) $resource_id,
                    $category,
                    $parameters
                );
            }
        }

        if (
            $category !== null ||
            $subcategory !== null ||
            $year !== null ||
            $month !== null ||
            count($search_parameters) > 0 ||
            count($filter_parameters) > 0
        ) {
            return $this->filteredSummary(
                (int) $resource_type_id,
                (int) $resource_id,
                $category,
                $subcategory,
                $year,
                $month,
                $parameters,
                (count($search_parameters) > 0 ? $search_parameters : []),
                (count($filter_parameters) > 0 ? $filter_parameters : [])
            );
        }

        return $this->tcoSummary(
            (int) $resource_type_id,
            (int) $resource_id,
            $parameters
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
        $cache_control = new Cache\Control($this->user_id);
        $cache_control->setTtlOneDay();

        $cache_summary = new Cache\Summary();
        $cache_summary->setFromCache($cache_control->get(request()->getRequestUri()));

        if ($cache_summary->valid() === false) {

            $summary = $this->model->summary(
                $resource_type_id,
                $resource_id,
                $parameters
            );

            $collection = [
                'total' => number_format($summary[0]['total'], 2, '.', '')
            ];

            $headers = new Headers();
            $headers->addCacheControl($cache_control->visibility(), $cache_control->ttl())->
                addETag($collection)->
                addParameters(Parameter\Request::xHeader());

            $cache_summary->create($collection, $headers->headers());
            $cache_control->put(request()->getRequestUri(), $cache_summary->content());
        }

        return response()->json($cache_summary->collection(), 200, $cache_summary->headers());
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
        $cache_control = new Cache\Control($this->user_id);
        $cache_control->setTtlOneDay();

        $cache_summary = new Cache\Summary();
        $cache_summary->setFromCache($cache_control->get(request()->getRequestUri()));

        if ($cache_summary->valid() === false) {

            $summary = $this->model->yearsSummary(
                $resource_type_id,
                $resource_id,
                $parameters
            );

            $collection = array_map(
                static function ($year) {
                    return (new ItemYearTransformer($year))->toArray();
                },
                $summary
            );

            $headers = new Headers();
            $headers->addCacheControl($cache_control->visibility(), $cache_control->ttl())->
                addETag($collection)->
                addParameters(Parameter\Request::xHeader());

            $cache_summary->create($collection, $headers->headers());
            $cache_control->put(request()->getRequestUri(), $cache_summary->content());
        }

        return response()->json($cache_summary->collection(), 200, $cache_summary->headers());
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
        $cache_control = new Cache\Control($this->user_id);
        $cache_control->setTtlOneDay();

        $cache_summary = new Cache\Summary();
        $cache_summary->setFromCache($cache_control->get(request()->getRequestUri()));

        if ($cache_summary->valid() === false) {

            $summary = $this->model->yearSummary(
                $resource_type_id,
                $resource_id,
                $year,
                $parameters
            );

            $collection = (new ItemYearTransformer($summary[0]))->toArray();

            $headers = new Headers();
            $headers->addCacheControl($cache_control->visibility(), $cache_control->ttl())->
                addETag($collection)->
                addParameters(Parameter\Request::xHeader());

            $cache_summary->create($collection, $headers->headers());
            $cache_control->put(request()->getRequestUri(), $cache_summary->content());
        }

        return response()->json($cache_summary->collection(), 200, $cache_summary->headers());
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
        $cache_control = new Cache\Control($this->user_id);
        $cache_control->setTtlOneDay();

        $cache_summary = new Cache\Summary();
        $cache_summary->setFromCache($cache_control->get(request()->getRequestUri()));

        if ($cache_summary->valid() === false) {

            $summary = $this->model->monthsSummary(
                $resource_type_id,
                $resource_id,
                $year,
                $parameters
            );

            $collection = array_map(
                static function ($month) {
                    return (new ItemMonthTransformer($month))->toArray();
                },
                $summary
            );

            $headers = new Headers();
            $headers->addCacheControl($cache_control->visibility(), $cache_control->ttl())->
                addETag($collection)->
                addParameters(Parameter\Request::xHeader());

            $cache_summary->create($collection, $headers->headers());
            $cache_control->put(request()->getRequestUri(), $cache_summary->content());
        }

        return response()->json($cache_summary->collection(), 200, $cache_summary->headers());
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
        $cache_control = new Cache\Control($this->user_id);
        $cache_control->setTtlOneDay();

        $cache_summary = new Cache\Summary();
        $cache_summary->setFromCache($cache_control->get(request()->getRequestUri()));

        if ($cache_summary->valid() === false) {

            $summary = $this->model->monthSummary(
                $resource_type_id,
                $resource_id,
                $year,
                $month,
                $parameters
            );

            $collection = (new ItemMonthTransformer($summary[0]))->toArray();

            $headers = new Headers();
            $headers->addCacheControl($cache_control->visibility(), $cache_control->ttl())->
                addETag($collection)->
                addParameters(Parameter\Request::xHeader());

            $cache_summary->create($collection, $headers->headers());
            $cache_control->put(request()->getRequestUri(), $cache_summary->content());
        }

        return response()->json($cache_summary->collection(), 200, $cache_summary->headers());
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
        $cache_control = new Cache\Control($this->user_id);
        $cache_control->setTtlOneDay();

        $cache_summary = new Cache\Summary();
        $cache_summary->setFromCache($cache_control->get(request()->getRequestUri()));

        if ($cache_summary->valid() === false) {
            $summary = $this->model->categoriesSummary(
                $resource_type_id,
                $resource_id,
                $parameters
            );

            $collection = array_map(
                static function ($category) {
                    return (new ItemCategoryTransformer($category))->toArray();
                },
                $summary
            );

            $headers = new Headers();
            $headers->addCacheControl($cache_control->visibility(), $cache_control->ttl())->
                addETag($collection)->
                addParameters(Parameter\Request::xHeader());

            $cache_summary->create($collection, $headers->headers());
            $cache_control->put(request()->getRequestUri(), $cache_summary->content());
        }

        return response()->json($cache_summary->collection(), 200, $cache_summary->headers());
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
     * @param array $filter_parameters
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
        array $search_parameters = [],
        array $filter_parameters = []
    ): JsonResponse
    {
        $cache_control = new Cache\Control($this->user_id);
        $cache_control->setTtlOneDay();

        $cache_summary = new Cache\Summary();
        $cache_summary->setFromCache($cache_control->get(request()->getRequestUri()));

        if ($cache_summary->valid() === false) {

            $summary = $this->model->filteredSummary(
                $resource_type_id,
                $resource_id,
                $category_id,
                $subcategory_id,
                $year,
                $month,
                $parameters,
                $search_parameters,
                $filter_parameters
            );

            $collection = [
                'total' => number_format($summary[0]['total'], 2, '.', '')
            ];

            $headers = new Headers();
            $headers->addCacheControl($cache_control->visibility(), $cache_control->ttl())->
                addETag($collection)->
                addParameters(Parameter\Request::xHeader());

            $cache_summary->create($collection, $headers->headers());
            $cache_control->put(request()->getRequestUri(), $cache_summary->content());
        }

        return response()->json($cache_summary->collection(), 200, $cache_summary->headers());
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
        $cache_control = new Cache\Control($this->user_id);
        $cache_control->setTtlOneDay();

        $cache_summary = new Cache\Summary();
        $cache_summary->setFromCache($cache_control->get(request()->getRequestUri()));

        if ($cache_summary->valid() === false) {

            $summary = $this->model->categorySummary(
                $resource_type_id,
                $resource_id,
                $category_id,
                $parameters
            );

            $collection = (new ItemCategoryTransformer($summary[0]))->toArray();

            $headers = new Headers();
            $headers->addCacheControl($cache_control->visibility(), $cache_control->ttl())->
                addETag($collection)->
                addParameters(Parameter\Request::xHeader());

            $cache_summary->create($collection, $headers->headers());
            $cache_control->put(request()->getRequestUri(), $cache_summary->content());
        }

        return response()->json($cache_summary->collection(), 200, $cache_summary->headers());
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
        $cache_control = new Cache\Control($this->user_id);
        $cache_control->setTtlOneDay();

        $cache_summary = new Cache\Summary();
        $cache_summary->setFromCache($cache_control->get(request()->getRequestUri()));

        if ($cache_summary->valid() === false) {

            $summary = $this->model->subCategoriesSummary(
                $resource_type_id,
                $resource_id,
                $category_id,
                $parameters
            );

            $collection = array_map(
                static function ($subcategory) {
                    return (new ItemSubcategoryTransformer($subcategory))->toArray();
                },
                $summary
            );

            $headers = new Headers();
            $headers->addCacheControl($cache_control->visibility(), $cache_control->ttl())->
                addETag($collection)->
                addParameters(Parameter\Request::xHeader());

            $cache_summary->create($collection, $headers->headers());
            $cache_control->put(request()->getRequestUri(), $cache_summary->content());
        }

        return response()->json($cache_summary->collection(), 200, $cache_summary->headers());
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
        $cache_control = new Cache\Control($this->user_id);
        $cache_control->setTtlOneDay();

        $cache_summary = new Cache\Summary();
        $cache_summary->setFromCache($cache_control->get(request()->getRequestUri()));

        if ($cache_summary->valid() === false) {

            $summary = $this->model->subCategorySummary(
                $resource_type_id,
                $resource_id,
                $category_id,
                $subcategory_id,
                $parameters
            );

            $collection = (new ItemSubcategoryTransformer($summary[0]))->toArray();

            $headers = new Headers();
            $headers->addCacheControl($cache_control->visibility(), $cache_control->ttl())->
                addETag($collection)->
                addParameters(Parameter\Request::xHeader());

            $cache_summary->create($collection, $headers->headers());
            $cache_control->put(request()->getRequestUri(), $cache_summary->content());
        }

        return response()->json($cache_summary->collection(), 200, $cache_summary->headers());
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
        Route\Validate::resource(
            $resource_type_id,
            $resource_id,
            $this->permitted_resource_types
        );

        $item_interface = Factory::summaryItem($resource_type_id);

        $permissions = Route\Permission::resource(
            $resource_type_id,
            $resource_id,
            $this->permitted_resource_types
        );

        $get = Get::init()->
            setParameters($item_interface->collectionParametersConfig())->
            setSearchable($item_interface->searchParametersConfig())->
            setFilterable($item_interface->filterParametersConfig())->
            setDescription('route-descriptions.summary_GET_resource-type_resource_items')->
            setAuthenticationStatus($permissions['view'])->
            option();

        return $this->optionsResponse($get, 200);
    }
}
