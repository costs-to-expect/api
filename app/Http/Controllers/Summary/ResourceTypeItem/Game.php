<?php

namespace App\Http\Controllers\Summary\ResourceTypeItem;

use App\Models\Transformers\Item\Summary\Game as GameTransformer;
use App\Models\Transformers\Item\Summary\GameItemByResource;
use App\Request\Validate\Boolean;
use App\Response\Cache;
use Illuminate\Http\JsonResponse;

class Game extends Item
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

        $this->model = new \App\ItemType\Game\SummaryResourceTypeModel();

        $this->fetchAllRequestParameters(new \App\ItemType\Game\Item());

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
        $cache_control = new Cache\Control(
            $this->permitted_user,
            $this->user_id
        );
        $cache_control->setTtlOneWeek();

        $cache_summary = new Cache\Summary();
        $cache_summary->setFromCache($cache_control->getByKey(request()->getRequestUri()));

        if ($cache_control->isRequestCacheable() === false || $cache_summary->valid() === false) {

            $summary = $this->model->filteredSummary(
                $this->resource_type_id,
                $this->parameters,
                $this->search_parameters
            );

            $collection = [];
            foreach ($summary as $subtotal) {
                $collection[] = (new GameTransformer($subtotal))->asArray();
            }

            $this->assignToCache(
                $summary,
                $collection,
                $cache_control,
                $cache_summary
            );
        }

        return response()->json($cache_summary->collection(), 200, $cache_summary->headers());
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
        $cache_control = new Cache\Control(
            $this->permitted_user,
            $this->user_id
        );
        $cache_control->setTtlOneWeek();

        $cache_summary = new Cache\Summary();
        $cache_summary->setFromCache($cache_control->getByKey(request()->getRequestUri()));

        if ($cache_control->isRequestCacheable() === false || $cache_summary->valid() === false) {

            $summary = $this->model->resourcesSummary(
                $this->resource_type_id,
                $this->parameters
            );

            $collection = (new GameItemByResource($summary))->asArray();

            $this->assignToCache(
                $summary,
                $collection,
                $cache_control,
                $cache_summary
            );
        }

        return response()->json($cache_summary->collection(), 200, $cache_summary->headers());
    }

    protected function summary(): JsonResponse
    {
        $cache_control = new Cache\Control(
            $this->permitted_user,
            $this->user_id
        );
        $cache_control->setTtlOneWeek();

        $cache_summary = new Cache\Summary();
        $cache_summary->setFromCache($cache_control->getByKey(request()->getRequestUri()));

        if ($cache_control->isRequestCacheable() === false || $cache_summary->valid() === false) {

            $summary = $this->model->summary(
                $this->resource_type_id,
                $this->parameters
            );

            $collection = [];
            foreach ($summary as $subtotal) {
                $collection[] = (new GameTransformer($subtotal))->asArray();
            }

            $this->assignToCache(
                $summary,
                $collection,
                $cache_control,
                $cache_summary
            );
        }

        return response()->json($cache_summary->collection(), 200, $cache_summary->headers());
    }
}
