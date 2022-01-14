<?php

namespace App\ItemType\SimpleItem;

use App\ItemType\ApiSummaryResponse as BaseSummaryResponse;
use App\ItemType\SimpleItem\Models\SummaryModel;
use App\ItemType\SimpleItem\Transformers\SummaryTransformer;
use Illuminate\Http\JsonResponse;

class ApiSummaryResponse extends BaseSummaryResponse
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

        $this->model = new SummaryModel();

        $this->fetchAllRequestParameters(new Item());

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
                $collection[] = (new SummaryTransformer($subtotal))->asArray();
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
        // Do nothing
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
                $collection[] = (new SummaryTransformer($subtotal))->asArray();
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
}
