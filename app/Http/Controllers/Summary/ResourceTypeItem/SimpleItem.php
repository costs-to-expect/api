<?php

namespace App\Http\Controllers\Summary\ResourceTypeItem;

use App\Models\Transformers\Item\Summary\SimpleItem as SimpleItemTransformer;
use App\Models\Transformers\Item\Summary\SimpleItemByResource;
use App\Response\Cache;
use App\Request\Parameter;
use App\Request\Validate\Boolean;
use App\Response\Header\Headers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;

class SimpleItem
{
    private int $resource_type_id;

    private bool $permitted_user;

    private ?int $user_id;

    private array $parameters;

    private array $decision_parameters = [];

    private array $filter_parameters;

    private array $search_parameters;

    private Model $model;

    public function __construct(
        int $resource_type_id,
        array $parameters,
        array $filter_parameters = [],
        array $search_parameters = [],
        bool $permitted_user = false,
        int $user_id = null
    )
    {
        $this->resource_type_id = $resource_type_id;

        $this->permitted_user = $permitted_user;
        $this->user_id = $user_id;

        $this->model = new \App\Models\ResourceTypeItem\Summary\SimpleItem();

        $this->parameters = $parameters;
        $this->filter_parameters = $filter_parameters;
        $this->search_parameters = $search_parameters;

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

    protected function assignToCache(
        array $summary,
        array $collection,
        Cache\Control $cache_control,
        Cache\Summary $cache_summary
    ): Cache\Summary
    {
        $headers = new Headers();

        $headers
            ->addCacheControl($cache_control->visibility(), $cache_control->ttl())
            ->addETag($collection)
            ->addParameters(Parameter\Request::xHeader())
            ->addFilters(Parameter\Filter::xHeader())
            ->addSearch(Parameter\Search::xHeader());

        if (array_key_exists(0, $summary)) {
            if (array_key_exists('last_updated', $summary[0]) === true) {
                $headers->addLastUpdated($summary[0]['last_updated']);
            }
            if (array_key_exists('total_count', $summary[0]) === true) {
                $headers->addTotalCount((int)$summary[0]['total_count']);
            }
        }

        $cache_summary->create($collection, $headers->headers());
        $cache_control->putByKey(request()->getRequestUri(), $cache_summary->content());

        return $cache_summary;
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
                $collection[] = (new SimpleItemTransformer($subtotal))->asArray();
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

            $collection = (new SimpleItemByResource($summary))->asArray();

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
                $collection[] = (new SimpleItemTransformer($subtotal))->asArray();
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
