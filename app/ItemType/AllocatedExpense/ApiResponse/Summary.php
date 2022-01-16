<?php

namespace App\ItemType\AllocatedExpense\ApiResponse;

use App\ItemType\AllocatedExpense\Item;
use App\ItemType\ApiSummaryResponse;
use App\Request\Validate\Boolean;
use Illuminate\Http\JsonResponse;
use function request;
use function response;

class Summary extends ApiSummaryResponse
{
    public function __construct(
        int $resource_type_id,
        int $resource_id,
        bool $permitted_user = false,
        int $user_id = null
    )
    {
        parent::__construct(
            $resource_type_id,
            $resource_id,
            $permitted_user,
            $user_id
        );

        $this->setUpCache();

        $this->model = new \App\ItemType\AllocatedExpense\Models\Summary();

        $this->shortCircuit(); // Skip working out which for obvious routes

        $this->fetchAllRequestParameters(new Item());

        $this->removeDecisionParameters();
    }

    protected function shortCircuit(): ?JsonResponse
    {
        $parameters = request()->getQueryString();
        if ($parameters === null) {
            $this->parameters = [];
            return $this->summary();
        }
        if ($parameters === 'categories=true') {
            $this->parameters = ['categories' => true];
            return $this->categoriesSummary();
        }
        if ($parameters === 'years=true') {
            $this->parameters = ['years' => true];
            return $this->yearsSummary();
        }

        return null;
    }

    public function response(): JsonResponse
    {
        if ($this->decision_parameters['years'] === true) {
            return $this->yearsSummary();
        }

        if (
            $this->decision_parameters['year'] !== null &&
            $this->decision_parameters['category'] === null &&
            $this->decision_parameters['subcategory'] === null &&
            count($this->search_parameters) === 0
        ) {
            if ($this->decision_parameters['months'] === true) {
                return $this->monthsSummary();
            }

            if ($this->decision_parameters['month'] !== null) {
                return $this->monthSummary();
            }

            return $this->yearSummary();
        }

        if ($this->decision_parameters['categories'] === true) {
            return $this->categoriesSummary();
        }

        if (
            $this->decision_parameters['category'] !== null &&
            $this->decision_parameters['year'] === null &&
            $this->decision_parameters['month'] === null &&
            count($this->search_parameters) === 0
        ) {
            if ($this->decision_parameters['subcategories'] === true) {
                return $this->subcategoriesSummary();
            }

            if ($this->decision_parameters['subcategory'] !== null) {
                return $this->subcategorySummary();
            }

            return $this->categorySummary();
        }

        if (
            $this->decision_parameters['category'] !== null ||
            $this->decision_parameters['subcategory'] !== null ||
            $this->decision_parameters['year'] !== null ||
            $this->decision_parameters['month'] !== null ||
            count($this->search_parameters) > 0 ||
            count($this->filter_parameters) > 0
        ) {
            return $this->filteredSummary();
        }

        return $this->summary();
    }

    protected function categoriesSummary(): JsonResponse
    {
        if ($this->cache_control->isRequestCacheable() === false || $this->cache_summary->valid() === false) {

            $summary = $this->model->categoriesSummary(
                $this->resource_type_id,
                $this->resource_id,
                $this->parameters
            );

            $collection = (new \App\ItemType\AllocatedExpense\Transformers\SummaryByCategory($summary))->asArray();

            $this->assignToCache(
                $summary,
                $collection,
                $this->cache_control,
                $this->cache_summary
            );
        }

        return response()->json($this->cache_summary->collection(), 200, $this->cache_summary->headers());
    }

    protected function categorySummary(): JsonResponse
    {
        if ($this->cache_control->isRequestCacheable() === false || $this->cache_summary->valid() === false) {

            $summary = $this->model->categorySummary(
                $this->resource_type_id,
                $this->resource_id,
                $this->decision_parameters['category'],
                $this->parameters
            );

            $collection = (new \App\ItemType\AllocatedExpense\Transformers\SummaryByCategory($summary))->asArray();

            if (count($collection) === 1) {
                $collection = $collection[0];
            } else {
                $collection = [];
            }

            $this->assignToCache(
                $summary,
                $collection,
                $this->cache_control,
                $this->cache_summary
            );
        }

        return response()->json($this->cache_summary->collection(), 200, $this->cache_summary->headers());
    }

    protected function filteredSummary(): JsonResponse
    {
        if ($this->cache_control->isRequestCacheable() === false || $this->cache_summary->valid() === false) {

            $summary = $this->model->filteredSummary(
                $this->resource_type_id,
                $this->resource_id,
                $this->decision_parameters['category'],
                $this->decision_parameters['subcategory'],
                $this->decision_parameters['year'],
                $this->decision_parameters['month'],
                $this->parameters,
                $this->search_parameters,
                $this->filter_parameters
            );

            $collection = [];
            foreach ($summary as $subtotal) {
                $collection[] = (new \App\ItemType\AllocatedExpense\Transformers\Summary($subtotal))->asArray();
            }

            $this->assignToCache(
                $summary,
                $collection,
                $this->cache_control,
                $this->cache_summary
            );
        }

        return response()->json($this->cache_summary->collection(), 200, $this->cache_summary->headers());
    }

    protected function monthsSummary(): JsonResponse
    {
        if ($this->cache_control->isRequestCacheable() === false || $this->cache_summary->valid() === false) {

            $summary = $this->model->monthsSummary(
                $this->resource_type_id,
                $this->resource_id,
                $this->decision_parameters['year'],
                $this->parameters
            );

            $collection = (new \App\ItemType\AllocatedExpense\Transformers\SummaryByMonth($summary))->asArray();

            $this->assignToCache(
                $summary,
                $collection,
                $this->cache_control,
                $this->cache_summary
            );
        }

        return response()->json($this->cache_summary->collection(), 200, $this->cache_summary->headers());
    }

    protected function monthSummary(): JsonResponse
    {
        if ($this->cache_control->isRequestCacheable() === false || $this->cache_summary->valid() === false) {

            $summary = $this->model->monthSummary(
                $this->resource_type_id,
                $this->resource_id,
                $this->decision_parameters['year'],
                $this->decision_parameters['month'],
                $this->parameters
            );

            $collection = (new \App\ItemType\AllocatedExpense\Transformers\SummaryByMonth($summary))->asArray();

            if (count($collection) === 1) {
                $collection = $collection[0];
            } else {
                $collection = [];
            }

            $this->assignToCache(
                $summary,
                $collection,
                $this->cache_control,
                $this->cache_summary
            );
        }

        return response()->json($this->cache_summary->collection(), 200, $this->cache_summary->headers());
    }

    protected function removeDecisionParameters(): void
    {
        $this->decision_parameters['years'] = false;
        $this->decision_parameters['months'] = false;
        $this->decision_parameters['categories'] = false;
        $this->decision_parameters['subcategories'] = false;
        $this->decision_parameters['year'] = null;
        $this->decision_parameters['month'] = null;
        $this->decision_parameters['category'] = null;
        $this->decision_parameters['subcategory'] = null;

        if (array_key_exists('years', $this->parameters) === true &&
            Boolean::convertedValue($this->parameters['years']) === true) {
            $this->decision_parameters['years'] = true;
        }

        if (array_key_exists('months', $this->parameters) === true &&
            Boolean::convertedValue($this->parameters['months']) === true) {
            $this->decision_parameters['months'] = true;
        }

        if (array_key_exists('categories', $this->parameters) === true &&
            Boolean::convertedValue($this->parameters['categories']) === true) {
            $this->decision_parameters['categories'] = true;
        }

        if (array_key_exists('subcategories', $this->parameters) === true &&
            Boolean::convertedValue($this->parameters['subcategories']) === true) {
            $this->decision_parameters['subcategories'] = true;
        }

        if (array_key_exists('year', $this->parameters) === true) {
            $this->decision_parameters['year'] = (int) $this->parameters['year'];
        }

        if (array_key_exists('month', $this->parameters) === true) {
            $this->decision_parameters['month'] = (int) $this->parameters['month'];
        }

        if (array_key_exists('category', $this->parameters) === true) {
            $this->decision_parameters['category'] = (int) $this->parameters['category'];
        }

        if (array_key_exists('subcategory', $this->parameters) === true) {
            $this->decision_parameters['subcategory'] = (int) $this->parameters['subcategory'];
        }

        unset(
            $this->parameters['years'],
            $this->parameters['year'],
            $this->parameters['months'],
            $this->parameters['month'],
            $this->parameters['categories'],
            $this->parameters['category'],
            $this->parameters['subcategories'],
            $this->parameters['subcategory']
        );
    }

    protected function subcategoriesSummary(): JsonResponse
    {
        if ($this->cache_control->isRequestCacheable() === false || $this->cache_summary->valid() === false) {

            $summary = $this->model->subCategoriesSummary(
                $this->resource_type_id,
                $this->resource_id,
                $this->decision_parameters['category'],
                $this->parameters
            );

            $collection = (new \App\ItemType\AllocatedExpense\Transformers\SummaryBySubcategory($summary))->asArray();

            $this->assignToCache(
                $summary,
                $collection,
                $this->cache_control,
                $this->cache_summary
            );
        }

        return response()->json($this->cache_summary->collection(), 200, $this->cache_summary->headers());
    }

    protected function subcategorySummary(): JsonResponse
    {
        if ($this->cache_control->isRequestCacheable() === false || $this->cache_summary->valid() === false) {

            $summary = $this->model->subCategorySummary(
                $this->resource_type_id,
                $this->resource_id,
                $this->decision_parameters['category'],
                $this->decision_parameters['subcategory'],
                $this->parameters
            );

            $collection = (new \App\ItemType\AllocatedExpense\Transformers\SummaryBySubcategory($summary))->asArray();

            if (count($collection) === 1) {
                $collection = $collection[0];
            } else {
                $collection = [];
            }

            $this->assignToCache(
                $summary,
                $collection,
                $this->cache_control,
                $this->cache_summary
            );
        }

        return response()->json($this->cache_summary->collection(), 200, $this->cache_summary->headers());
    }

    protected function summary(): JsonResponse
    {
        if ($this->cache_control->isRequestCacheable() === false || $this->cache_summary->valid() === false) {

            $summary = $this->model->summary(
                $this->resource_type_id,
                $this->resource_id,
                $this->parameters
            );

            $collection = [];
            foreach ($summary as $subtotal) {
                $collection[] = (new \App\ItemType\AllocatedExpense\Transformers\Summary($subtotal))->asArray();
            }

            $this->assignToCache(
                $summary,
                $collection,
                $this->cache_control,
                $this->cache_summary
            );
        }

        return response()->json($this->cache_summary->collection(), 200, $this->cache_summary->headers());
    }

    protected function yearsSummary(): JsonResponse
    {
        if ($this->cache_control->isRequestCacheable() === false || $this->cache_summary->valid() === false) {

            $summary = $this->model->yearsSummary(
                $this->resource_type_id,
                $this->resource_id,
                $this->parameters
            );

            $collection = (new \App\ItemType\AllocatedExpense\Transformers\SummaryByYear($summary))->asArray();

            $this->assignToCache(
                $summary,
                $collection,
                $this->cache_control,
                $this->cache_summary
            );
        }

        return response()->json($this->cache_summary->collection(), 200, $this->cache_summary->headers());
    }

    protected function yearSummary(): JsonResponse
    {
        if ($this->cache_control->isRequestCacheable() === false || $this->cache_summary->valid() === false) {

            $summary = $this->model->yearSummary(
                $this->resource_type_id,
                $this->resource_id,
                $this->decision_parameters['year'],
                $this->parameters
            );

            $collection = (new \App\ItemType\AllocatedExpense\Transformers\SummaryByYear($summary))->asArray();

            if (count($collection) === 1) {
                $collection = $collection[0];
            } else {
                $collection = [];
            }

            $this->assignToCache(
                $summary,
                $collection,
                $this->cache_control,
                $this->cache_summary
            );
        }

        return response()->json($this->cache_summary->collection(), 200, $this->cache_summary->headers());
    }

    // Overridden here, because we want a different TTL
    protected function setUpCache(): void
    {
        $this->cache_control = new \App\Cache\Control(
            $this->permitted_user,
            $this->user_id
        );

        if ($this->cache_control->visibility() === 'public') {
            $this->cache_control->setTtlOneWeek();
        } else {
            $this->cache_control->setTtlOneDay();
        }

        $this->cache_summary = new \App\Cache\Summary();
        $this->cache_summary->setFromCache($this->cache_control->getByKey(request()->getRequestUri()));
    }
}
