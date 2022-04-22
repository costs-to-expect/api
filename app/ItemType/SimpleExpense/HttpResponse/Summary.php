<?php

namespace App\ItemType\SimpleExpense\HttpResponse;

use App\HttpRequest\Parameter;
use App\HttpRequest\Validate\Boolean;
use App\ItemType\HttpResponse\ApiSummaryResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config as LaravelConfig;
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

        $this->model = new \App\ItemType\SimpleExpense\Models\Summary();

        $this->requestParameters();

        $this->removeDecisionParameters();
    }

    public function response(): JsonResponse
    {
        if ($this->decision_parameters['categories'] === true) {
            return $this->categoriesSummary();
        }

        if (
            $this->decision_parameters['category'] !== null &&
            count($this->filter_parameters) === 0 &&
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

            $collection = (new \App\ItemType\SimpleExpense\Transformer\SummaryByCategory($summary))->asArray();

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

            $collection = (new \App\ItemType\SimpleExpense\Transformer\SummaryByCategory($summary))->asArray();

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
                $this->parameters,
                $this->search_parameters,
                $this->filter_parameters
            );

            $collection = [];
            foreach ($summary as $subtotal) {
                $collection[] = (new \App\ItemType\SimpleExpense\Transformer\Summary($subtotal))->asArray();
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
        $this->decision_parameters['categories'] = false;
        $this->decision_parameters['subcategories'] = false;
        $this->decision_parameters['category'] = null;
        $this->decision_parameters['subcategory'] = null;

        if (array_key_exists('categories', $this->parameters) === true &&
            Boolean::convertedValue($this->parameters['categories']) === true) {
            $this->decision_parameters['categories'] = true;
        }

        if (array_key_exists('subcategories', $this->parameters) === true &&
            Boolean::convertedValue($this->parameters['subcategories']) === true) {
            $this->decision_parameters['subcategories'] = true;
        }

        if (array_key_exists('category', $this->parameters) === true) {
            $this->decision_parameters['category'] = (int) $this->parameters['category'];
        }

        if (array_key_exists('subcategory', $this->parameters) === true) {
            $this->decision_parameters['subcategory'] = (int) $this->parameters['subcategory'];
        }

        unset(
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

            $collection = (new \App\ItemType\SimpleExpense\Transformer\SummaryBySubcategory($summary))->asArray();

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

            $collection = (new \App\ItemType\SimpleExpense\Transformer\SummaryBySubcategory($summary))->asArray();

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
                $collection[] = (new \App\ItemType\SimpleExpense\Transformer\Summary($subtotal))->asArray();
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

    private function requestParameters(): void
    {
        $base_path = 'api.item-type-simple-expense';

        $this->parameters = Parameter\Request::fetch(
            array_keys(LaravelConfig::get($base_path . '.summary-parameters', [])),
            $this->resource_type_id,
            $this->resource_id
        );

        $this->search_parameters = Parameter\Search::fetch(
            LaravelConfig::get($base_path . '.summary-searchable', [])
        );

        $this->filter_parameters = Parameter\Filter::fetch(
            LaravelConfig::get($base_path . '.summary-filterable', [])
        );
    }
}
