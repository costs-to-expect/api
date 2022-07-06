<?php

declare(strict_types=1);

namespace App\ItemType\Game\HttpResponse;

use App\HttpRequest\Parameter\Filter;
use App\HttpRequest\Parameter\Request;
use App\HttpRequest\Parameter\Search;
use App\HttpRequest\Parameter\Sort;
use App\ItemType\HttpResponse\ApiResourceTypeItemResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config as LaravelConfig;
use function request;
use function response;

class ResourceTypeItem extends ApiResourceTypeItemResponse
{
    public function response(): JsonResponse
    {
        $this->requestParameters();

        $this->cache_control->setTtlOneWeek();

        $cache_collection = new \App\Cache\Collection();
        $cache_collection->setFromCache($this->cache_control->getByKey(request()->getRequestUri()));

        if (
            $this->cache_control->isRequestCacheable() === false ||
            $cache_collection->valid() === false
        ) {
            $model = new \App\ItemType\Game\Models\ResourceTypeItem();

            $total = $model->totalCount(
                $this->resource_type_id,
                $this->request_parameters,
                $this->search_parameters,
                $this->filter_parameters
            );

            $pagination_parameters = $this->pagination_parameters($total);

            $items = $model->paginatedCollection(
                $this->resource_type_id,
                $pagination_parameters['offset'],
                $pagination_parameters['limit'],
                $this->request_parameters,
                $this->search_parameters,
                $this->filter_parameters,
                $this->sort_fields
            );

            $last_updated = null;
            if (count($items) && array_key_exists('last_updated', $items[0])) {
                $last_updated = $items[0]['last_updated'];
            }

            $collection = array_map(
                static function ($item) {
                    return (new \App\ItemType\Game\Transformer\ResourceTypeItem($item))->asArray();
                },
                $items
            );

            $cache_collection->create(
                $total,
                $collection,
                $pagination_parameters,
                $this->headers(
                    $pagination_parameters,
                    count($items),
                    $total,
                    $collection,
                    $last_updated
                )
            );
            $this->cache_control->putByKey(request()->getRequestUri(), $cache_collection->content());
        }

        return response()->json($cache_collection->collection(), 200, $cache_collection->headers());
    }

    private function requestParameters(): void
    {
        $base_path = 'api.resource-type-item-type-game';

        $this->request_parameters = Request::fetch(
            array_keys(LaravelConfig::get($base_path . '.parameters', [])),
            $this->resource_type_id
        );

        $this->search_parameters = Search::fetch(
            LaravelConfig::get($base_path . '.searchable', [])
        );

        $this->filter_parameters = Filter::fetch(
            LaravelConfig::get($base_path . '.filterable', [])
        );

        $this->sort_fields = Sort::fetch(
            LaravelConfig::get($base_path . '.sortable', [])
        );
    }
}
