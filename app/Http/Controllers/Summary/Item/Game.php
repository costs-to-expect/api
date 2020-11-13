<?php

namespace App\Http\Controllers\Summary\Item;

use App\Response\Cache;
use App\Request\Parameter;
use App\Response\Header\Headers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;

class Game
{
    private int $resource_type_id;

    private int $resource_id;

    private bool $permitted_user;

    private ?int $user_id;

    private array $parameters;

    private array $decision_parameters = [];

    private array $filter_parameters;

    private array $search_parameters;

    private Model $model;

    public function __construct(
        int $resource_type_id,
        int $resource_id,
        bool $permitted_user = false,
        int $user_id = null
    )
    {
        $this->resource_type_id = $resource_type_id;
        $this->resource_id = $resource_id;

        $this->permitted_user = $permitted_user;
        $this->user_id = $user_id;

        $this->model = new \App\Models\Item\Summary\Game();

        $entity = new \App\Entity\Item\Game();

        $this->parameters = Parameter\Request::fetch(
            array_keys($entity->summaryRequestParameters()),
            $resource_type_id,
            $resource_id
        );

        $this->search_parameters = Parameter\Search::fetch(
            $entity->summarySearchParameters()
        );

        $this->filter_parameters = Parameter\Filter::fetch(
            $entity->summaryFilterParameters()
        );

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
                $this->resource_id,
                $this->parameters,
                $this->search_parameters,
                $this->filter_parameters
            );

            $collection = [];
            foreach ($summary as $subtotal) {
                $collection[] = (new \App\Models\Transformers\Item\Summary\Game($subtotal))->asArray();
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
        // Do nothing
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
                $this->resource_id,
                $this->parameters
            );

            $collection = [];
            foreach ($summary as $subtotal) {
                $collection[] = (new \App\Models\Transformers\Item\Summary\Game($subtotal))->asArray();
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
