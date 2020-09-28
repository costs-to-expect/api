<?php

namespace App\Http\Controllers\Summary;

use App\Entity\Item\Entity;
use App\Http\Controllers\Controller;
use App\Option\SummaryItemCollection;
use App\Request\Parameter;
use App\Request\Route;
use App\Request\Validate\Boolean;
use App\Response\Cache;
use App\Response\Header\Headers;
use Illuminate\Http\JsonResponse;

/**
 * Summary for the items route
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemView extends Controller
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

        $entity = Entity::item($resource_type_id);

        $this->model = $entity->summaryModel();

        $parameters = Parameter\Request::fetch(
            array_keys($entity->summaryRequestParameters()),
            (int)$resource_type_id,
            (int)$resource_id
        );

        $years = false;
        $months = false;
        $categories = false;
        $subcategories = false;
        $year = null;
        $month = null;
        $category = null;
        $subcategory = null;

        if (array_key_exists('years', $parameters) === true &&
            Boolean::convertedValue($parameters['years']) === true) {
            $years = true;
        }

        if (array_key_exists('months', $parameters) === true &&
            Boolean::convertedValue($parameters['months']) === true) {
            $months = true;
        }

        if (array_key_exists('categories', $parameters) === true &&
            Boolean::convertedValue($parameters['categories']) === true) {
            $categories = true;
        }

        if (array_key_exists('subcategories', $parameters) === true &&
            Boolean::convertedValue($parameters['subcategories']) === true) {
            $subcategories = true;
        }

        if (array_key_exists('year', $parameters) === true) {
            $year = (int)$parameters['year'];
        }

        if (array_key_exists('month', $parameters) === true) {
            $month = (int)$parameters['month'];
        }

        if (array_key_exists('category', $parameters) === true) {
            $category = (int)$parameters['category'];
        }

        if (array_key_exists('subcategory', $parameters) === true) {
            $subcategory = (int)$parameters['subcategory'];
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
            $entity->summarySearchParameters()
        );

        $filter_parameters = Parameter\Filter::fetch(
            $entity->filterParameters()
        );

        if ($years === true) {
            return $this->yearsSummary(
                (int)$resource_type_id,
                (int)$resource_id,
                $parameters
            );
        }

        if (
            $year !== null &&
            $category === null &&
            $subcategory === null &&
            count($search_parameters) === 0
        ) {
            if ($months === true) {
                return $this->monthsSummary(
                    (int)$resource_type_id,
                    (int)$resource_id,
                    $year,
                    $parameters
                );
            }

            if ($month !== null) {
                return $this->monthSummary(
                    (int)$resource_type_id,
                    (int)$resource_id,
                    $year,
                    $month,
                    $parameters
                );
            }

            return $this->yearSummary(
                (int)$resource_type_id,
                (int)$resource_id,
                $year,
                $parameters
            );
        }

        if ($categories === true) {
            return $this->categoriesSummary(
                (int)$resource_type_id,
                (int)$resource_id,
                $parameters
            );
        }

        if (
            $category !== null &&
            $year === null &&
            $month === null &&
            count($search_parameters) === 0
        ) {
            if ($subcategories === true) {
                return $this->subcategoriesSummary(
                    (int)$resource_type_id,
                    (int)$resource_id,
                    $category,
                    $parameters
                );
            }

            if ($subcategory !== null) {
                return $this->subcategorySummary(
                    (int)$resource_type_id,
                    (int)$resource_id,
                    $category,
                    $subcategory,
                    $parameters
                );
            }

            return $this->categorySummary(
                (int)$resource_type_id,
                (int)$resource_id,
                $category,
                $parameters
            );
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
                (int)$resource_type_id,
                (int)$resource_id,
                $category,
                $subcategory,
                $year,
                $month,
                $parameters,
                (count($search_parameters) > 0 ? $search_parameters : []),
                (count($filter_parameters) > 0 ? $filter_parameters : [])
            );
        }

        return $this->summary(
            (int)$resource_type_id,
            (int)$resource_id,
            $parameters
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
    ): JsonResponse {
        $cache_control = new Cache\Control(
            $this->user_id,
            in_array($resource_type_id, $this->permitted_resource_types, true)
        );
        $cache_control->setTtlOneWeek();

        $cache_summary = new Cache\Summary();
        $cache_summary->setFromCache($cache_control->get(request()->getRequestUri()));

        if ($cache_control->cacheable() === false || $cache_summary->valid() === false) {

            $summary = $this->model->yearsSummary(
                $resource_type_id,
                $resource_id,
                $parameters
            );

            $entity = Entity::item($resource_type_id);

            $collection = $entity->summaryTransformerByYear($summary)->asArray();

            $this->assignContentToCache(
                $summary,
                $collection,
                $cache_control,
                $cache_summary
            );
        }

        return response()->json($cache_summary->collection(), 200, $cache_summary->headers());
    }

    private function assignContentToCache(
        array $summary,
        array $collection,
        Cache\Control $cache_control,
        Cache\Summary $cache_summary
    ): \App\Response\Cache\Summary {
        $headers = new Headers();
        $headers->addCacheControl($cache_control->visibility(), $cache_control->ttl())->
            addETag($collection)->
            addParameters(Parameter\Request::xHeader())->
            addFilters(Parameter\Filter::xHeader())->
            addSearch(Parameter\Search::xHeader());

        if (array_key_exists(0, $summary)) {
            if (array_key_exists('last_updated', $summary[0]) === true) {
                $headers->addLastUpdated($summary[0]['last_updated']);
            }
            if (array_key_exists('total_count', $summary[0]) === true) {
                $headers->addTotalCount((int)$summary[0]['total_count']);
            }
        }

        $cache_summary->create($collection, $headers->headers());
        $cache_control->put(request()->getRequestUri(), $cache_summary->content());

        return $cache_summary;
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
    ): JsonResponse {
        $cache_control = new Cache\Control(
            $this->user_id,
            in_array($resource_type_id, $this->permitted_resource_types, true)
        );
        $cache_control->setTtlOneWeek();

        $cache_summary = new Cache\Summary();
        $cache_summary->setFromCache($cache_control->get(request()->getRequestUri()));

        if ($cache_control->cacheable() === false || $cache_summary->valid() === false) {

            $summary = $this->model->monthsSummary(
                $resource_type_id,
                $resource_id,
                $year,
                $parameters
            );

            $entity = Entity::item($resource_type_id);

            $collection = $entity->summaryTransformerByMonth($summary)->asArray();

            $this->assignContentToCache(
                $summary,
                $collection,
                $cache_control,
                $cache_summary
            );
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
    ): JsonResponse {
        $cache_control = new Cache\Control(
            $this->user_id,
            in_array($resource_type_id, $this->permitted_resource_types, true)
        );
        $cache_control->setTtlOneWeek();

        $cache_summary = new Cache\Summary();
        $cache_summary->setFromCache($cache_control->get(request()->getRequestUri()));

        if ($cache_control->cacheable() === false || $cache_summary->valid() === false) {

            $summary = $this->model->monthSummary(
                $resource_type_id,
                $resource_id,
                $year,
                $month,
                $parameters
            );

            $entity = Entity::item($resource_type_id);

            $collection = $entity->summaryTransformerByMonth($summary)->asArray();

            if (count($collection) === 1) {
                $collection = $collection[0];
            } else {
                $collection = [];
            }

            $this->assignContentToCache(
                $summary,
                $collection,
                $cache_control,
                $cache_summary
            );
        }

        return response()->json($cache_summary->collection(), 200, $cache_summary->headers());
    }

    /**
     * Return the total cost for a specific year
     *
     * @param int $resource_type_id ,
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
    ): JsonResponse {
        $cache_control = new Cache\Control(
            $this->user_id,
            in_array($resource_type_id, $this->permitted_resource_types, true)
        );
        $cache_control->setTtlOneWeek();

        $cache_summary = new Cache\Summary();
        $cache_summary->setFromCache($cache_control->get(request()->getRequestUri()));

        if ($cache_control->cacheable() === false || $cache_summary->valid() === false) {

            $summary = $this->model->yearSummary(
                $resource_type_id,
                $resource_id,
                $year,
                $parameters
            );

            $entity = Entity::item($resource_type_id);

            $collection = $entity->summaryTransformerByYear($summary)->asArray();

            if (count($collection) === 1) {
                $collection = $collection[0];
            } else {
                $collection = [];
            }

            $this->assignContentToCache(
                $summary,
                $collection,
                $cache_control,
                $cache_summary
            );
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
    ): JsonResponse {
        $cache_control = new Cache\Control(
            $this->user_id,
            in_array($resource_type_id, $this->permitted_resource_types, true)
        );
        $cache_control->setTtlOneWeek();

        $cache_summary = new Cache\Summary();
        $cache_summary->setFromCache($cache_control->get(request()->getRequestUri()));

        if ($cache_control->cacheable() === false || $cache_summary->valid() === false) {
            $summary = $this->model->categoriesSummary(
                $resource_type_id,
                $resource_id,
                $parameters
            );

            $entity = Entity::item($resource_type_id);

            $collection = $entity->summaryTransformerByCategory($summary)->asArray();

            $this->assignContentToCache(
                $summary,
                $collection,
                $cache_control,
                $cache_summary
            );
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
    ): JsonResponse {
        $cache_control = new Cache\Control(
            $this->user_id,
            in_array($resource_type_id, $this->permitted_resource_types, true)
        );
        $cache_control->setTtlOneWeek();

        $cache_summary = new Cache\Summary();
        $cache_summary->setFromCache($cache_control->get(request()->getRequestUri()));

        if ($cache_control->cacheable() === false || $cache_summary->valid() === false) {

            $summary = $this->model->subCategoriesSummary(
                $resource_type_id,
                $resource_id,
                $category_id,
                $parameters
            );

            $entity = Entity::item($resource_type_id);

            $collection = $entity->summaryTransformerBySubcategory($summary)->asArray();

            $this->assignContentToCache(
                $summary,
                $collection,
                $cache_control,
                $cache_summary
            );
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
    ): JsonResponse {
        $cache_control = new Cache\Control(
            $this->user_id,
            in_array($resource_type_id, $this->permitted_resource_types, true)
        );
        $cache_control->setTtlOneWeek();

        $cache_summary = new Cache\Summary();
        $cache_summary->setFromCache($cache_control->get(request()->getRequestUri()));

        if ($cache_control->cacheable() === false || $cache_summary->valid() === false) {

            $summary = $this->model->subCategorySummary(
                $resource_type_id,
                $resource_id,
                $category_id,
                $subcategory_id,
                $parameters
            );

            $entity = Entity::item($resource_type_id);

            $collection = $entity->summaryTransformerBySubcategory($summary)->asArray();

            if (count($collection) === 1) {
                $collection = $collection[0];
            } else {
                $collection = [];
            }

            $this->assignContentToCache(
                $summary,
                $collection,
                $cache_control,
                $cache_summary
            );
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
    ): JsonResponse {
        $cache_control = new Cache\Control(
            $this->user_id,
            in_array($resource_type_id, $this->permitted_resource_types, true)
        );
        $cache_control->setTtlOneWeek();

        $cache_summary = new Cache\Summary();
        $cache_summary->setFromCache($cache_control->get(request()->getRequestUri()));

        if ($cache_control->cacheable() === false || $cache_summary->valid() === false) {

            $summary = $this->model->categorySummary(
                $resource_type_id,
                $resource_id,
                $category_id,
                $parameters
            );

            $entity = Entity::item($resource_type_id);

            $collection = $entity->summaryTransformerByCategory($summary)->asArray();

            if (count($collection) === 1) {
                $collection = $collection[0];
            } else {
                $collection = [];
            }

            $this->assignContentToCache(
                $summary,
                $collection,
                $cache_control,
                $cache_summary
            );
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
    ): JsonResponse {
        $cache_control = new Cache\Control(
            $this->user_id,
            in_array($resource_type_id, $this->permitted_resource_types, true)
        );
        $cache_control->setTtlOneWeek();

        $cache_summary = new Cache\Summary();
        $cache_summary->setFromCache($cache_control->get(request()->getRequestUri()));

        if ($cache_control->cacheable() === false || $cache_summary->valid() === false) {

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

            $entity = Entity::item($resource_type_id);

            $collection = [];
            foreach ($summary as $subtotal) {
                $collection[] = $entity->summaryTransformer($subtotal)->asArray();
            }

            $this->assignContentToCache(
                $summary,
                $collection,
                $cache_control,
                $cache_summary
            );
        }

        return response()->json($cache_summary->collection(), 200, $cache_summary->headers());
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
    private function summary(
        int $resource_type_id,
        int $resource_id,
        array $parameters
    ): JsonResponse {
        $cache_control = new Cache\Control(
            $this->user_id,
            in_array($resource_type_id, $this->permitted_resource_types, true)
        );
        $cache_control->setTtlOneWeek();

        $cache_summary = new Cache\Summary();
        $cache_summary->setFromCache($cache_control->get(request()->getRequestUri()));

        if ($cache_control->cacheable() === false || $cache_summary->valid() === false) {

            $summary = $this->model->summary(
                $resource_type_id,
                $resource_id,
                $parameters
            );

            $entity = Entity::item($resource_type_id);

            $collection = [];
            foreach ($summary as $subtotal) {
                $collection[] = $entity->summaryTransformer($subtotal)->asArray();
            }

            $this->assignContentToCache(
                $summary,
                $collection,
                $cache_control,
                $cache_summary
            );
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
    public function optionsIndex(string $resource_type_id, string $resource_id): JsonResponse
    {
        Route\Validate::resource(
            $resource_type_id,
            $resource_id,
            $this->permitted_resource_types
        );

        $entity = Entity::item($resource_type_id);

        $permissions = Route\Permission::resource(
            $resource_type_id,
            $resource_id,
            $this->permitted_resource_types
        );

        $defined_parameters = Parameter\Request::fetch(
            array_keys($entity->requestParameters()),
            (int) $resource_type_id,
            (int) $resource_id
        );

        $allowed_values = (new \App\Option\AllowedValues\ResourceItem($entity))->allowedValues(
            $resource_type_id,
            $resource_id,
            $this->permitted_resource_types,
            $this->include_public,
            $entity->requestParameters(),
            $defined_parameters
        );

        $response = new SummaryItemCollection($permissions);

        return $response->setEntity($entity)
            ->setAllowedValues($allowed_values)
            ->create()
            ->response();
    }
}
