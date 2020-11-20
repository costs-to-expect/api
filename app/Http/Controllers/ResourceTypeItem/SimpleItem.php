<?php
declare(strict_types=1);

namespace App\Http\Controllers\ResourceTypeItem;

use App\ItemType\Item;
use App\Models\Transformers\ResourceTypeItem\SimpleItem as Transformer;
use App\Response\Cache;
use Illuminate\Http\JsonResponse;

class SimpleItem extends Item
{
    public function response(): JsonResponse
    {
        $this->fetchAllRequestParameters(
            new \App\ItemType\SimpleItem\Item()
        );

        $this->cache_control->setTtlOneMonth();

        $cache_collection = new Cache\Collection();
        $cache_collection->setFromCache($this->cache_control->getByKey(request()->getRequestUri()));

        if (
            $this->cache_control->isRequestCacheable() === false ||
            $cache_collection->valid() === false
        ) {
            $model = new \App\ItemType\SimpleItem\ResourceTypeModel();

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

            $collection = array_map(
                static function ($item) {
                    return (new Transformer($item))->asArray();
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
                    $collection
                )
            );
            $this->cache_control->putByKey(request()->getRequestUri(), $cache_collection->content());
        }

        return response()->json($cache_collection->collection(), 200, $cache_collection->headers());
    }
}
