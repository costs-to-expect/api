<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\HttpResponse\Response;
use App\ItemType\Select;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemDataView extends Controller
{
    public function index(
        Request $request,
        $resource_type_id,
        $resource_id,
        $item_id
    ): JsonResponse
    {
        if ($this->hasViewAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.resource'));
        }

        $item_type = Select::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense' => Response::notSupported(),
            'game' => $this->gameCollection($request, (int) $resource_type_id, (int) $resource_id, (int) $item_id),
            default => throw new \OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    public function gameCollection(
        Request $request,
        int $resource_type_id,
        int $resource_id,
        int $item_id
    ): JsonResponse
    {

    }
}