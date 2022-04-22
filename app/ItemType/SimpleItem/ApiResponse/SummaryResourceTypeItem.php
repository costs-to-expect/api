<?php

namespace App\ItemType\SimpleItem\ApiResponse;

use App\ItemType\ApiSummaryResourceTypeItemResponse;
use App\HttpRequest\Parameter;
use App\HttpRequest\Validate\Boolean;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config as LaravelConfig;
use function response;

class SummaryResourceTypeItem extends ApiSummaryResourceTypeItemResponse
{
    public function __construct(
        int $resource_type_id,
        bool $permitted_user = false,
        int $user_id = null
    )
    {
        parent::__construct(
            $resource_type_id,
            $permitted_user,
            $user_id
        );
        
        $this->setUpCache();

        $this->model = new \App\ItemType\SimpleItem\Models\SummaryResourceTypeItem();

        $this->requestParameters();

        $this->removeDecisionParameters();
    }

    public function response(): JsonResponse
    {
        if ($this->decision_parameters['resources'] === true) {
            return $this->resourcesSummary();
        }

        if (
            count($this->search_parameters) > 0 ||
            count($this->filter_parameters) > 0
        ) {
            return $this->filteredSummary();
        }

        return $this->summary();
    }

    protected function filteredSummary(): JsonResponse
    {
        if ($this->cache_control->isRequestCacheable() === false || $this->cache_summary->valid() === false) {

            $summary = $this->model->filteredSummary(
                $this->resource_type_id,
                $this->parameters,
                $this->search_parameters
            );

            $collection = [];
            foreach ($summary as $subtotal) {
                $collection[] = (new \App\ItemType\SimpleItem\Transformers\Summary($subtotal))->asArray();
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
        $this->decision_parameters['resources'] = false;

        if (array_key_exists('resources', $this->parameters) === true &&
            Boolean::convertedValue($this->parameters['resources']) === true) {
            $this->decision_parameters['resources'] = true;
        }

        unset(
            $this->parameters['resources'],
        );
    }

    protected function resourcesSummary(): JsonResponse
    {
        if ($this->cache_control->isRequestCacheable() === false || $this->cache_summary->valid() === false) {

            $summary = $this->model->resourcesSummary(
                $this->resource_type_id,
                $this->parameters
            );

            $collection = (new \App\ItemType\SimpleItem\Transformers\SummaryByResource($summary))->asArray();

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
                $this->parameters
            );

            $collection = [];
            foreach ($summary as $subtotal) {
                $collection[] = (new \App\ItemType\SimpleItem\Transformers\Summary($subtotal))->asArray();
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
        $base_path = 'api.resource-type-item-type-simple-item';

        $this->parameters = Parameter\Request::fetch(
            array_keys(LaravelConfig::get($base_path . '.summary-parameters', [])),
            $this->resource_type_id
        );

        $this->search_parameters = Parameter\Search::fetch(
            LaravelConfig::get($base_path . '.summary-searchable', [])
        );

        $this->filter_parameters = Parameter\Filter::fetch(
            LaravelConfig::get($base_path . '.summary-filterable', [])
        );
    }
}
