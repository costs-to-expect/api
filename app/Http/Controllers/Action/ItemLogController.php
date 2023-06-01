<?php
declare(strict_types=1);

namespace App\Http\Controllers\Action;

use App\Http\Controllers\Controller;
use App\ItemType\Select;
use App\Models\ItemLog;
use Illuminate\Http\Request;
use App\HttpResponse\Response;
use App\HttpRequest\Validate\ItemLog as ItemLogValidator;
use App\Transformer\ItemLog as ItemLogTransformer;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemLogController extends Controller
{
    public function create(
        Request $request,
        $resource_type_id,
        $resource_id,
        $item_id
    ): JsonResponse
    {
        $item_type = Select::itemType((int) $resource_type_id);
        if ($item_type === 'allocated-expense' || $item_type === 'budget') {
            return Response::notSupported();
        }

        if ($this->hasWriteAccessToResourceType((int) $resource_type_id) === false) {
            return Response::notFoundOrNotAccessible(trans('entities.item-game'));
        }

        $game = (new \App\ItemType\Game\Models\Item())->single(
            $resource_type_id,
            $resource_id,
            $item_id
        );

        if ($game === null) {
            return Response::notFoundOrNotAccessible(trans('entities.item-game'));
        }

        $validator = (new ItemLogValidator())->create();

        if ($validator->fails()) {
            return Response::validationErrors($validator);
        }

        try {
            $item_log = DB::transaction(static function () use ($request, $game) {
                $item_log = new ItemLog([
                    'item_id' => $game['item_id'],
                    'message' => $request->input('message'),
                    'parameters' => $request->input('parameters')
                ]);
                $item_log->save();

                return $item_log;
            });
        } catch (Exception $e) {
            return Response::failedToSaveModelForCreate($e);
        }

        return response()->json(
            (new ItemLogTransformer((new ItemLog())->instanceToArray($item_log)))->asArray(),
            201
        );
    }
}
