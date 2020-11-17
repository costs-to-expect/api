<?php

namespace App\Http\Controllers\Summary\ResourceTypeItem;

use App\Response\Cache;
use App\Request\Parameter;
use App\Response\Header\Headers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;

abstract class Item
{
    protected int $resource_type_id;

    protected bool $permitted_user;

    protected ?int $user_id;

    protected array $parameters;

    protected array $decision_parameters = [];

    protected array $filter_parameters;

    protected array $search_parameters;

    protected Model $model;

    public function __construct(
        int $resource_type_id,
        bool $permitted_user = false,
        int $user_id = null
    )
    {
        $this->resource_type_id = $resource_type_id;

        $this->permitted_user = $permitted_user;
        $this->user_id = $user_id;
    }

    abstract public function response(): JsonResponse;

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

    abstract protected function removeDecisionParameters(): void;

    protected function fetchAllRequestParameters(
        $request_parameters,
        $search_parameters,
        $filter_parameters
    ): void
    {
        $this->parameters = Parameter\Request::fetch(
            array_keys($request_parameters),
            $this->resource_type_id
        );

        $this->search_parameters = Parameter\Search::fetch(
            $search_parameters
        );

        $this->filter_parameters = Parameter\Filter::fetch(
            $filter_parameters
        );
    }
}
