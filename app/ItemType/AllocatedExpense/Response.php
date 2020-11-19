<?php
declare(strict_types=1);

namespace App\ItemType\AllocatedExpense;

use App\ItemType\Response as ItemTypeResponse;
use App\Models\Transformers\Item\AllocatedExpense as Transformer;
use App\Response\Cache;
use Illuminate\Http\JsonResponse;

class Response extends ItemTypeResponse
{
    public function collectionResponse(): JsonResponse
    {
        $this->fetchAllRequestParameters(
            new \App\Entity\Item\AllocatedExpense()
        );

        if ($this->cache_control->visibility() === 'public') {
            $this->cache_control->setTtlOneWeek();
        } else {
            $this->cache_control->setTtlOneDay();
        }

        $cache_collection = new Cache\Collection();
        $cache_collection->setFromCache($this->cache_control->getByKey(request()->getRequestUri()));

        if (
            $this->cache_control->isRequestCacheable() === false ||
            $cache_collection->valid() === false
        ) {
            $model = new \App\Models\Item\AllocatedExpense();

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
                    $collection
                )
            );
            $this->cache_control->putByKey(request()->getRequestUri(), $cache_collection->content());
        }

        return response()->json($cache_collection->collection(), 200, $cache_collection->headers());
    }

    public function showResponse(int $item_id): JsonResponse
    {
        $this->fetchAllRequestParameters(
            new \App\Entity\Item\AllocatedExpense()
        );

        $item = (new \App\Models\Item\AllocatedExpense())->single(
            $this->resource_type_id,
            $this->resource_id,
            $item_id,
            $this->request_parameters
        );

        if ($item === null) {
            return \App\Response\Responses::notFound(trans('entities.item'));
        }

        return response()->json(
            (new Transformer($item))->asArray(),
            200,
            $this->showHeaders()
        );
    }
}
