<?php

namespace App\Http\Controllers;

use App\Models\ItemData;
use Illuminate\Http\Request;
use App\HttpResponse\Response;
use App\HttpRequest\Validate\ItemData as ItemDataValidator;
use App\Transformer\ItemData as ItemDataTransformer;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemDataManage extends Controller
{
    public function create(
        Request $request,
        $resource_type_id,
        $resource_id,
        $item_id
    ): JsonResponse
    {
        if ($this->hasWriteAccessToResourceType((int) $resource_type_id) === false) {
            return Response::notFoundOrNotAccessible(trans('entities.resource-type'));
        }

        $game = (new \App\ItemType\Game\Models\Item())->single(
            $resource_type_id,
            $resource_id,
            $item_id
        );

        if ($game === null) {
            return Response::notFoundOrNotAccessible(trans('entities.item-game'));
        }

        $validator = (new ItemDataValidator())->create([
            'item_id' => $item_id
        ]);

        if ($validator->fails()) {
            return Response::validationErrors($validator);
        }

        try {
            $item_data = DB::transaction(function () use ($request, $game) {
                $item_data = new ItemData([
                    'item_id' => $game['item_id'],
                    'key' => $request->input('key'),
                    'value' => $request->input('value')
                ]);
                $item_data->save();

                return $item_data;
            });
        } catch (Exception $e) {
            return Response::failedToSaveModelForCreate($e);
        }

        return response()->json(
            (new ItemDataTransformer((new ItemData())->instanceToArray($item_data)))->asArray(),
            201
        );
    }
}
