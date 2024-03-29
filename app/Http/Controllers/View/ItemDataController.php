<?php
declare(strict_types=1);

namespace App\Http\Controllers\View;

use App\Http\Controllers\Controller;
use App\HttpOptionResponse\ItemDataCollection;
use App\HttpOptionResponse\ItemDataItem;
use App\HttpResponse\Header;
use App\HttpResponse\Response;
use App\ItemType\Select;
use App\Models\ItemData;
use App\Transformer\ItemData as ItemDataTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2023
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemDataController extends Controller
{
    public function index(
        Request $request,
        $resource_type_id,
        $resource_id,
        $item_id
    ): JsonResponse
    {
        if ($this->hasViewAccessToResourceType((int) $resource_type_id) === false) {
            return Response::notFoundOrNotAccessible(trans('entities.item'));
        }

        $item_type = Select::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense', 'budget', 'budget-pro' => Response::notSupported(),
            'game' => $this->gameCollection((int) $resource_type_id, (int) $resource_id, (int) $item_id),
            default => throw new \OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    public function optionsIndex(
        Request $request,
        $resource_type_id,
        $resource_id,
        $item_id
    ): JsonResponse
    {
        if ($this->hasViewAccessToResourceType((int) $resource_type_id) === false) {
            return Response::notFoundOrNotAccessible(trans('entities.item'));
        }

        $item_type = Select::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense', 'budget', 'budget-pro' => Response::notSupported(),
            'game' => $this->gameOptionsIndex((int) $resource_type_id, (int) $resource_id, (int) $item_id),
            default => throw new \OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    public function optionsShow(
        Request $request,
        $resource_type_id,
        $resource_id,
        $item_id,
        string $key
    ): JsonResponse
    {
        if ($this->hasViewAccessToResourceType((int) $resource_type_id) === false) {
            return Response::notFoundOrNotAccessible(trans('entities.item'));
        }

        $item_type = Select::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense', 'budget', 'budget-pro' => Response::notSupported(),
            'game' => $this->gameOptionsShow((int) $resource_type_id, (int) $resource_id, (int) $item_id, $key),
            default => throw new \OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    public function show(
        $resource_type_id,
        $resource_id,
        $item_id,
        string $key
    ): JsonResponse {
        if ($this->hasViewAccessToResourceType((int) $resource_type_id) === false) {
            return Response::notFoundOrNotAccessible(trans('entities.resource'));
        }

        $item_type = Select::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense', 'budget', 'budget-pro' => Response::notSupported(),
            'game' => $this->gameShow((int) $resource_type_id, (int) $resource_id, (int) $item_id, $key),
            default => throw new \OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    private function gameCollection(
        int $resource_type_id,
        int $resource_id,
        int $item_id
    ): JsonResponse
    {
        $total = (new ItemData())->totalCount(
            $resource_type_id,
            $resource_id,
            $item_id,
            $this->viewable_resource_types
        );

        $item_data = (new ItemData())->collection(
            $resource_type_id,
            $resource_id,
            $item_id,
            $this->viewable_resource_types
        );

        $last_updated = null;
        if (count($item_data) > 0 && array_key_exists('last_updated', $item_data[0])) {
            $last_updated = $item_data[0]['last_updated'];
        }

        $headers = (new Header())->item()->addTotalCount($total);

        if ($last_updated !== null) {
            $headers->addLastUpdated($last_updated);
        }

        $collection = array_map(
            static function ($data) {
                return (new ItemDataTransformer($data))->asArray();
            },
            $item_data
        );

        return response()->json($collection, 200, $headers->headers());
    }

    private function gameOptionsIndex(
        int $resource_type_id,
        int $resource_id,
        int $item_id,
    ): JsonResponse
    {
        $game = (new \App\ItemType\Game\Models\Item())->single(
            $resource_type_id,
            $resource_id,
            $item_id,
            $this->viewable_resource_types
        );

        if ($game === null) {
            return Response::notFoundOrNotAccessible(trans('entities.item-game'));
        }

        return (new ItemDataCollection($this->permissions($resource_type_id)))
            ->create()
            ->response();
    }

    private function gameOptionsShow(
        int $resource_type_id,
        int $resource_id,
        int $item_id,
        string $key
    ): JsonResponse
    {
        $data = (new ItemData())->single(
            $resource_type_id,
            $resource_id,
            $item_id,
            $key,
            $this->viewable_resource_types
        );

        if ($data === null) {
            return \App\HttpResponse\Response::notFound(trans('entities.item-data'));
        }

        return (new ItemDataItem($this->permissions($resource_type_id)))
            ->create()
            ->response();
    }

    private function gameShow(
        int $resource_type_id,
        int $resource_id,
        int $item_id,
        string $key
    ): JsonResponse
    {
        $data = (new ItemData())->single(
            $resource_type_id,
            $resource_id,
            $item_id,
            $key,
            $this->viewable_resource_types
        );

        if ($data === null) {
            return Response::notFound(trans('entities.item-data'));
        }

        return response()->json(
            (new ItemDataTransformer($data))->asArray(),
            200,
            (new Header())->item()->headers()
        );
    }
}