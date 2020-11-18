<?php
declare(strict_types=1);

namespace App\Http\Controllers\Item;

use App\Models\Transformers\Item\SimpleItem as Transformer;
use App\Response\Cache;
use Illuminate\Http\JsonResponse;

class SimpleItem extends Item
{
    public function collectionResponse(): JsonResponse
    {
        $this->fetchAllRequestParameters(
            new \App\Entity\Item\SimpleItem()
        );

        $this->cache_control->setTtlOneMonth();

        $cache_collection = new Cache\Collection();
        $cache_collection->setFromCache($this->cache_control->getByKey(request()->getRequestUri()));

        if (
            $this->cache_control->isRequestCacheable() === false ||
            $cache_collection->valid() === false
        ) {
            $model = new \App\Models\Item\SimpleItem();

            $total = $model->totalCount(
                $this->resource_type_id,
                $this->resource_id,
                $this->request_parameters,
                $this->search_parameters,
                $this->filter_parameters
            );

            $pagination_parameters = $this->pagination_parameters($total);

            $items = $model->paginatedCollection(
                $this->resource_type_id,
                $this->resource_id,
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

    public function showResponse(int $item_id): JsonResponse
    {
        return response()->json([], 200, []);
    }
}
