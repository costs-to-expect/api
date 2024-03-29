<?php

declare(strict_types=1);

namespace App\ItemType\Game\HttpResponse;

use App\HttpRequest\Parameter\Filter;
use App\HttpRequest\Parameter\Request;
use App\HttpRequest\Parameter\Search;
use App\HttpRequest\Parameter\Sort;
use App\HttpResponse\Response;
use App\ItemType\HttpResponse\ApiItemResponse;
use App\Models\ItemCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config as LaravelConfig;

use function request;
use function response;
use function trans;

class Item extends ApiItemResponse
{
    public function collectionResponse(): JsonResponse
    {
        $this->requestParameters();

        $this->cache_control->setTtlOneWeek();

        $cache_collection = new \App\Cache\Response\Collection();
        $cache_collection->setFromCache($this->cache_control->getByKey(request()->getRequestUri()));

        if (
            $this->cache_control->isRequestCacheable() === false ||
            $cache_collection->valid() === false
        ) {
            $model = new \App\ItemType\Game\Models\Item();

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

            $players = [];
            if (array_key_exists('include-players', $this->request_parameters) === true) {
                $item_ids = [];
                foreach ($items as $item) {
                    $item_ids[] = (int)$item['item_id'];
                }
                if (count($item_ids) > 0) {
                    $assigned_players = (new ItemCategory())->collectionByItemIds(
                        $this->resource_type_id,
                        $this->resource_id,
                        $item_ids
                    );

                    foreach ($assigned_players as $player) {
                        $players[$player['item_category_item_id']][] = $player;
                    }
                }
            }

            $collection = array_map(
                function ($item) use ($players) {
                    return (new \App\ItemType\Game\Transformer\Item(
                        $item,
                        [
                            'resource_type_id' => $this->resource_type_id,
                            'resource_id' => $this->resource_id,
                            'players' => $players
                        ]
                    ))->asArray();
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
        $this->requestParameters();

        $item = (new \App\ItemType\Game\Models\Item())->single(
            $this->resource_type_id,
            $this->resource_id,
            $item_id,
            $this->request_parameters
        );

        $players = [];
        if (array_key_exists('include-players', $this->request_parameters) === true) {
            $players[$item_id] = (new ItemCategory())->paginatedCollection(
                $this->resource_type_id,
                $this->resource_id,
                $item_id
            );
        }

        if ($item === null) {
            return Response::notFound(trans('entities.item'));
        }

        return response()->json(
            (new \App\ItemType\Game\Transformer\Item(
                $item,
                [
                    'resource_type_id' => $this->resource_type_id,
                    'resource_id' => $this->resource_id,
                    'players' => $players
                ]
            ))->asArray(),
            200,
            $this->showHeaders()
        );
    }

    protected function requestParameters(): void
    {
        $base_path = 'api.item-type-game';

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
