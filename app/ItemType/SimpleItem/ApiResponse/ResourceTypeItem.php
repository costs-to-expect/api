<?php
declare(strict_types=1);

namespace App\ItemType\SimpleItem\ApiResponse;

use App\ItemType\ApiResourceTypeResponse as BaseResourceTypeResponse;
use App\ItemType\SimpleItem\Item;
use Illuminate\Http\JsonResponse;
use function request;
use function response;

class ResourceTypeItem extends BaseResourceTypeResponse
{
    public function response(): JsonResponse
    {
        $this->cache_control->setTtlOneMonth();

        $cache_collection = new \App\Cache\Collection();
        $cache_collection->setFromCache($this->cache_control->getByKey(request()->getRequestUri()));

        if (
            $this->cache_control->isRequestCacheable() === false ||
            $cache_collection->valid() === false
        ) {
            $model = new \App\ItemType\SimpleItem\Models\ResourceTypeItem();

            $this->fetchAllRequestParameters(
                new Item()
            );

            $total = $model->totalCount(
                $this->resource_type_id,
                $this->search_parameters,
                $this->filter_parameters
            );

            $pagination_parameters = $this->pagination_parameters($total);

            $items = $model->paginatedCollection(
                $this->resource_type_id,
                $pagination_parameters['offset'],
                $pagination_parameters['limit'],
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
                    return (new \App\ItemType\SimpleItem\Transformers\ResourceTypeItem($item))->asArray();
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
}
