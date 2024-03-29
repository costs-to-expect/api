<?php

namespace App\ItemType\Game\HttpResponse;

use App\HttpRequest\Parameter;
use App\ItemType\HttpResponse\ApiSummaryResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config as LaravelConfig;
use function response;

class Summary extends ApiSummaryResponse
{
    public function __construct(
        int $resource_type_id,
        int $resource_id,
        int $user_id = null
    ) {
        parent::__construct(
            $resource_type_id,
            $resource_id,
            $user_id
        );

        $this->setUpCache();

        $this->model = new \App\ItemType\Game\Models\Summary();

        $this->requestParameters();

        $this->removeDecisionParameters();
    }

    public function response(): JsonResponse
    {
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
                $this->resource_id,
                $this->parameters,
                $this->search_parameters,
                $this->filter_parameters
            );

            $collection = [];
            foreach ($summary as $subtotal) {
                $collection[] = (new \App\ItemType\Game\Transformer\Summary($subtotal))->asArray();
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
        // Nothing here
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
                $collection[] = (new \App\ItemType\Game\Transformer\Summary($subtotal))->asArray();
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
        $base_path = 'api.item-type-game';

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
