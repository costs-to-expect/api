<?php
declare(strict_types=1);

namespace App\Http\Controllers\Item;

use App\Request\Parameter\Filter;
use App\Request\Parameter\Request;
use App\Request\Parameter\Search;
use App\Request\Parameter\Sort;
use App\Response\Cache;
use App\Response\Header\Header;
use App\Response\Header\Headers;
use App\Response\Pagination as UtilityPagination;
use Illuminate\Http\JsonResponse;

abstract class Item
{
    protected int $resource_type_id;

    protected int $resource_id;

    protected bool $permitted_user;

    protected ?int $user_id;

    protected Cache\Control $cache_control;

    protected array $request_parameters;
    protected array $search_parameters;
    protected array $filter_parameters;
    protected array $sort_fields;

    public function __construct(
        int $resource_type_id,
        int $resource_id,
        bool $permitted_user,
        ?int $user_id
    )
    {
        $this->resource_type_id = $resource_type_id;
        $this->resource_id = $resource_id;
        $this->permitted_user = $permitted_user;
        $this->user_id = $user_id;

        $this->cache_control = new Cache\Control(
            $this->permitted_user,
            $this->user_id
        );
    }

    abstract public function collectionResponse(): JsonResponse;
    abstract public function showResponse(int $item_id): JsonResponse;

    protected function collectionHeaders(
        array $pagination_parameters,
        int $count,
        int $total,
        array $collection
    ): array
    {
        $headers = new Headers();
        $headers
            ->collection($pagination_parameters, $count, $total)
            ->addCacheControl($this->cache_control->visibility(), $this->cache_control->ttl())
            ->addETag($collection)
            ->addSearch(Search::xHeader())
            ->addSort(Sort::xHeader())
            ->addParameters(Request::xHeader())
            ->addFilters(Filter::xHeader());

        return $headers->headers();
    }

    protected function showHeaders(): array
    {
        $headers = new Header();
        $headers->item();

        return $headers->headers();
    }

    protected function fetchAllRequestParameters(
        \App\Entity\Item\Item $entity
    ): void
    {
        $this->request_parameters = Request::fetch(
            array_keys($entity->requestParameters()),
            $this->resource_type_id
        );

        $this->search_parameters = Search::fetch(
            $entity->searchParameters()
        );

        $this->filter_parameters = Filter::fetch(
            $entity->filterParameters()
        );

        $this->sort_fields = Sort::fetch(
            $entity->sortParameters()
        );
    }

    protected function pagination_parameters(int $total): array
    {
        $pagination = new UtilityPagination(request()->path(), $total);
        return $pagination
            ->allowPaginationOverride(false)
            ->setSearchParameters($this->search_parameters)
            ->setSortParameters($this->sort_fields)
            ->setParameters($this->request_parameters)
            ->setFilteringParameters($this->filter_parameters)
            ->parameters();
    }
}
