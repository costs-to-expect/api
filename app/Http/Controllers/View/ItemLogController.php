<?php

declare(strict_types=1);

namespace App\Http\Controllers\View;

use App\Http\Controllers\Controller;
use App\HttpOptionResponse\ItemLogCollection;
use App\HttpOptionResponse\ItemLogItem;
use App\HttpResponse\Header;
use App\HttpResponse\Response;
use App\ItemType\Game\Models\Item;
use App\ItemType\Select;
use App\Models\ItemLog;
use App\Transformer\ItemLog as ItemLogTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OutOfRangeException;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemLogController extends Controller
{
    public function index(
        Request $request,
        $resource_type_id,
        $resource_id,
        $item_id
    ): JsonResponse {
        if ($this->hasViewAccessToResourceType((int)$resource_type_id) === false) {
            return Response::notFoundOrNotAccessible(trans('entities.item'));
        }

        $item_type = Select::itemType((int)$resource_type_id);

        return match ($item_type) {
            'allocated-expense', 'budget' => Response::notSupported(),
            'game' => $this->gameCollection((int) $resource_type_id, (int) $resource_id, (int) $item_id),
            default => throw new OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    private function gameCollection(
        int $resource_type_id,
        int $resource_id,
        int $item_id
    ): JsonResponse
    {
        $total = (new ItemLog())->totalCount(
            $resource_type_id,
            $resource_id,
            $item_id,
            $this->viewable_resource_types
        );

        $item_log = (new ItemLog())->collection(
            $resource_type_id,
            $resource_id,
            $item_id,
            $this->viewable_resource_types
        );

        $last_updated = null;
        if (count($item_log) > 0 && array_key_exists('last_updated', $item_log[0])) {
            $last_updated = $item_log[0]['last_updated'];
        }

        $headers = (new Header())->item()->addTotalCount($total);

        if ($last_updated !== null) {
            $headers->addLastUpdated($last_updated);
        }

        $collection = array_map(
            static function ($data) {
                return (new ItemLogTransformer($data))->asArray();
            },
            $item_log
        );

        return response()->json($collection, 200, $headers->headers());
    }

    public function optionsIndex(
        Request $request,
        $resource_type_id,
        $resource_id,
        $item_id
    ): JsonResponse {
        if ($this->hasViewAccessToResourceType((int)$resource_type_id) === false) {
            return Response::notFoundOrNotAccessible(trans('entities.item'));
        }

        $item_type = Select::itemType((int)$resource_type_id);

        return match ($item_type) {
            'allocated-expense', 'budget' => Response::notSupported(),
            'game' => $this->gameOptionsIndex((int) $resource_type_id, (int) $resource_id, (int) $item_id),
            default => throw new OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    private function gameOptionsIndex(
        int $resource_type_id,
        int $resource_id,
        int $item_id,
    ): JsonResponse {
        $game = (new Item())->single(
            $resource_type_id,
            $resource_id,
            $item_id,
            $this->viewable_resource_types
        );

        if ($game === null) {
            return Response::notFoundOrNotAccessible(trans('entities.item-game'));
        }

        return (new ItemLogCollection($this->permissions($resource_type_id)))
            ->create()
            ->response();
    }

    public function optionsShow(
        Request $request,
        $resource_type_id,
        $resource_id,
        $item_id,
        $item_log_id
    ): JsonResponse {
        if ($this->hasViewAccessToResourceType((int)$resource_type_id) === false) {
            return Response::notFoundOrNotAccessible(trans('entities.item'));
        }

        $item_type = Select::itemType((int)$resource_type_id);

        return match ($item_type) {
            'allocated-expense' => Response::notSupported(),
            'game' => $this->gameOptionsShow((int) $resource_type_id, (int) $resource_id, (int) $item_id, (int) $item_log_id),
            default => throw new OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    private function gameOptionsShow(
        int $resource_type_id,
        int $resource_id,
        int $item_id,
        int $item_log_id
    ): JsonResponse {
        $data = (new ItemLog())->single(
            $resource_type_id,
            $resource_id,
            $item_id,
            $item_log_id,
            $this->viewable_resource_types
        );

        if ($data === null) {
            return Response::notFound(trans('entities.item-data'));
        }

        return (new ItemLogItem($this->permissions($resource_type_id)))
            ->create()
            ->response();
    }

    public function show(
        $resource_type_id,
        $resource_id,
        $item_id,
        $item_log_id
    ): JsonResponse {
        if ($this->hasViewAccessToResourceType((int)$resource_type_id) === false) {
            return Response::notFoundOrNotAccessible(trans('entities.resource'));
        }

        $item_type = Select::itemType((int)$resource_type_id);

        return match ($item_type) {
            'allocated-expense', 'budget' => Response::notSupported(),
            'game' => $this->gameShow((int) $resource_type_id, (int) $resource_id, (int) $item_id, (int) $item_log_id),
            default => throw new OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    private function gameShow(
        int $resource_type_id,
        int $resource_id,
        int $item_id,
        int $item_log_id
    ): JsonResponse {
        $data = (new ItemLog())->single(
            $resource_type_id,
            $resource_id,
            $item_id,
            $item_log_id,
            $this->viewable_resource_types
        );

        if ($data === null) {
            return Response::notFound(trans('entities.item-data'));
        }

        return response()->json(
            (new ItemLogTransformer($data))->asArray(),
            200,
            (new Header())->item()->headers()
        );
    }
}