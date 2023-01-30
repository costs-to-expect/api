<?php

namespace App\Http\Controllers\View;

use App\Http\Controllers\Controller;
use App\HttpOptionResponse\ItemPartialTransfer\AllocatedExpense;
use App\HttpOptionResponse\ItemPartialTransfer\AllocatedExpenseCollection;
use App\HttpOptionResponse\ItemPartialTransfer\AllocatedExpenseTransfer;
use App\HttpRequest\Parameter;
use App\HttpResponse\Header;
use App\HttpResponse\Response;
use App\ItemType\Select;
use App\Models\ItemPartialTransfer;
use App\Transformer\ItemPartialTransfer as ItemPartialTransferTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemPartialTransferController extends Controller
{
    public function index($resource_type_id): JsonResponse
    {
        if ($this->hasViewAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.resource-type'));
        }

        $item_type = Select::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense' => $this->allocatedExpenseCollection((int) $resource_type_id),
            'game' => Response::notSupported(),
            default => throw new \OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    private function allocatedExpenseCollection(int $resource_type_id): JsonResponse
    {
        $cache_control = new \App\Cache\Control($this->user_id);
        $cache_control->setTtlOneWeek();

        $cache_collection = new \App\Cache\Response\Collection();
        $cache_collection->setFromCache($cache_control->getByKey(request()->getRequestUri()));

        if ($cache_control->isRequestCacheable() === false || $cache_collection->valid() === false) {
            $parameters = Parameter\Request::fetch(
                array_keys(Config::get('api.item-partial-transfer.parameters'))
            );

            $total = (new ItemPartialTransfer())->total(
                $resource_type_id,
                $this->viewable_resource_types,
                $parameters
            );

            $pagination = new \App\HttpResponse\Pagination(request()->path(), $total);
            $pagination_parameters = $pagination->allowPaginationOverride($this->allow_entire_collection)->
            setParameters($parameters)->
            parameters();

            $transfers = (new ItemPartialTransfer())->paginatedCollection(
                $resource_type_id,
                $this->viewable_resource_types,
                $pagination_parameters['offset'],
                $pagination_parameters['limit'],
                $parameters
            );

            $collection = array_map(
                static function ($transfer) {
                    return (new ItemPartialTransferTransformer($transfer))->asArray();
                },
                $transfers
            );

            $headers = new Header();
            $headers->collection($pagination_parameters, count($transfers), $total)
                ->addCacheControl($cache_control->visibility(), $cache_control->ttl())
                ->addETag($collection);

            $cache_collection->create($total, $collection, $pagination_parameters, $headers->headers());
            $cache_control->putByKey(request()->getRequestUri(), $cache_collection->content());
        }

        return response()->json($cache_collection->collection(), 200, $cache_collection->headers());
    }

    public function show(
        $resource_type_id,
        $item_partial_transfer_id
    ): JsonResponse {
        if ($this->hasViewAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.resource-type'));
        }

        $item_type = Select::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense' => $this->allocatedExpense((int) $resource_type_id, (int) $item_partial_transfer_id),
            'game' => Response::notSupported(),
            default => throw new \OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    private function allocatedExpense($resource_type_id, $item_partial_transfer_id): JsonResponse
    {
        $item_partial_transfer = (new ItemPartialTransfer())->single(
            (int) $resource_type_id,
            (int) $item_partial_transfer_id
        );

        if ($item_partial_transfer === null) {
            return \App\HttpResponse\Response::notFound(trans('entities.item_partial_transfer'));
        }

        $headers = new Header();
        $headers->item();

        return response()->json(
            (new ItemPartialTransferTransformer($item_partial_transfer))->asArray(),
            200,
            $headers->headers()
        );
    }

    public function optionsIndex($resource_type_id): JsonResponse
    {
        if ($this->hasViewAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.item'));
        }

        $item_type = Select::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense' => $this->optionsAllocatedExpenseCollection((int) $resource_type_id),
            'game' => Response::notSupported(),
            default => throw new \OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    private function optionsAllocatedExpenseCollection(int $resource_type_id): JsonResponse
    {
        $response = new AllocatedExpenseCollection($this->permissions($resource_type_id));

        return $response->create()->response();
    }

    public function optionsShow($resource_type_id, $item_partial_transfer_id): JsonResponse
    {
        if ($this->hasViewAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.item-partial-transfer'));
        }

        $item_type = Select::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense' => $this->optionsAllocatedExpenseShow((int) $resource_type_id),
            'game' => Response::notSupported(),
            default => throw new \OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    private function optionsAllocatedExpenseShow(int $resource_type_id): JsonResponse
    {
        $response = new AllocatedExpense($this->permissions((int) $resource_type_id));

        return $response->create()->response();
    }

    public function optionsTransfer(
        string $resource_type_id,
        string $resource_id,
        string $item_id
    ): JsonResponse {
        if ($this->hasViewAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.item'));
        }

        $item_type = Select::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense' => $this->optionsAllocatedExpenseTransfer((int) $resource_type_id, (int) $resource_id),
            'game' => Response::notSupported(),
            default => throw new \OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    private function optionsAllocatedExpenseTransfer(
        int $resource_type_id,
        int $resource_id
    ): JsonResponse {
        $response = new AllocatedExpenseTransfer($this->permissions($resource_type_id));

        return $response->setAllowedValuesForFields(
            (new \App\Models\AllowedValue\Resource())->allowedValues(
                    $resource_type_id,
                    $resource_id
                )
        )->
            create()->
            response();
    }
}
