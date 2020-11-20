<?php
declare(strict_types=1);

namespace App\ItemType\AllocatedExpense;

use App\ItemType\ResourceTypeResponse as BaseResourceTypeResponse;
use App\ItemType\AllocatedExpense\ResourceTypeTransformer as Transformer;
use App\Response\Cache;
use Illuminate\Http\JsonResponse;

class ResourceTypeResponse extends BaseResourceTypeResponse
{
    public function response(): JsonResponse
    {
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
            $model = new ResourceTypeModel();

            $this->fetchAllRequestParameters(
                new Item()
            );

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
