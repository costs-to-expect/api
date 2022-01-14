<?php
declare(strict_types=1);

namespace App\ItemType\Game;

use App\ItemType\ApiResponse as ItemTypeResponse;
use App\ItemType\Game\Models\Item;
use App\Response\Responses;
use Illuminate\Http\JsonResponse;

class ApiResponse extends ItemTypeResponse
{
    public function collectionResponse(): JsonResponse
    {
        $this->cache_control->setTtlOneWeek();

        $cache_collection = new \App\Cache\Collection();
        $cache_collection->setFromCache($this->cache_control->getByKey(request()->getRequestUri()));

        if (
            $this->cache_control->isRequestCacheable() === false ||
            $cache_collection->valid() === false
        ) {
            $model = new Item();

            $this->fetchAllRequestParameters(
                new \App\ItemType\Game\Item()
            );

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
                    return (new Transformer($item))->asArray();
                },
                $items
            );

            $cache_collection->create(
                $total,
                $collection,
                $pagination_parameters,
                $this->collectionHeaders(
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

    public function showResponse(int $item_id): JsonResponse
    {
        $this->fetchAllRequestParameters(
            new \App\ItemType\Game\Item()
        );

        $item = (new Item())->single(
            $this->resource_type_id,
            $this->resource_id,
            $item_id,
            $this->request_parameters
        );

        if ($item === null) {
            return Responses::notFound(trans('entities.item'));
        }

        return response()->json(
            (new Transformer($item))->asArray(),
            200,
            $this->showHeaders()
        );
    }
}
