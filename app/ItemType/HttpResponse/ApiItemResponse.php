<?php

declare(strict_types=1);

namespace App\ItemType\HttpResponse;

use App\HttpRequest\Parameter\Filter;
use App\HttpRequest\Parameter\Request;
use App\HttpRequest\Parameter\Search;
use App\HttpRequest\Parameter\Sort;
use App\HttpResponse\Header;
use App\HttpResponse\Pagination as UtilityPagination;
use Illuminate\Http\JsonResponse;

abstract class ApiItemResponse
{
    protected int $resource_type_id;

    protected int $resource_id;

    protected ?int $user_id;

    protected \App\Cache\Control $cache_control;

    protected array $request_parameters;
    protected array $search_parameters;
    protected array $filter_parameters;
    protected array $sort_fields;

    public function __construct(
        int $resource_type_id,
        int $resource_id,
        ?int $user_id
    ) {
        $this->resource_type_id = $resource_type_id;
        $this->resource_id = $resource_id;
        $this->user_id = $user_id;

        $this->cache_control = new \App\Cache\Control($this->user_id);
    }

    abstract public function collectionResponse(): JsonResponse;
    abstract public function showResponse(int $item_id): JsonResponse;

    protected function collectionHeaders(
        array $pagination_parameters,
        int $count,
        int $total,
        array $collection,
        string $last_updated = null
    ): array {
        $headers = new Header();
        $headers
            ->collection($pagination_parameters, $count, $total)
            ->addCacheControl($this->cache_control->visibility(), $this->cache_control->ttl())
            ->addETag($collection)
            ->addSearch(Search::xHeader())
            ->addSort(Sort::xHeader())
            ->addParameters(Request::xHeader())
            ->addFilter(Filter::xHeader());

        if ($last_updated !== null) {
            $headers->addLastUpdated($last_updated);
        }

        return $headers->headers();
    }

    protected function showHeaders(): array
    {
        $headers = new Header();
        $headers->item();

        return $headers->headers();
    }

    protected function pagination_parameters(int $total, bool $allow_override = false): array
    {
        $pagination = new UtilityPagination(request()->path(), $total);
        return $pagination->allowPaginationOverride($allow_override)
            ->setSearchParameters($this->search_parameters)
            ->setSortParameters($this->sort_fields)
            ->setParameters($this->request_parameters)
            ->setFilteringParameters($this->filter_parameters)
            ->parameters();
    }
}
