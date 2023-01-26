<?php
declare(strict_types=1);

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\ItemType\Select;
use App\Models\ItemData;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\HttpResponse\Response;
use App\HttpRequest\Validate\ItemData as ItemDataValidator;
use App\Transformer\ItemData as ItemDataTransformer;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemDataController extends Controller
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

    public function delete(
        Request $request,
        $resource_type_id,
        $resource_id,
        $item_id,
        string $key
    ): JsonResponse
    {
        $item_type = Select::itemType((int) $resource_type_id);
        if ($item_type === 'allocated-expense') {
            return Response::notSupported();
        }

        $game_data = (new ItemData())->instance(
            $resource_type_id,
            $resource_id,
            $item_id,
            $key,
            $this->permitted_resource_types
        );

        if ($game_data === null) {
            return Response::notFoundOrNotAccessible(trans('entities.item-data'));
        }

        try {
            DB::transaction(static function () use ($game_data) {
                $game_data->delete();
            });

            return Response::successNoContent();
        } catch (QueryException $e) {
            return Response::foreignKeyConstraintError($e);
        } catch (Exception $e) {
            return Response::notFound(trans('entities.item-data'), $e);
        }
    }

    public function update(
        Request $request,
        $resource_type_id,
        $resource_id,
        $item_id,
        string $key
    ): JsonResponse
    {
        $item_type = Select::itemType((int) $resource_type_id);
        if ($item_type === 'allocated-expense') {
            return Response::notSupported();
        }

        $data = (new ItemData())->instance(
            (int) $resource_type_id,
            (int) $resource_id,
            (int) $item_id,
            $key,
            $this->permitted_resource_types
        );

        if ($data === null) {
            return Response::notFoundOrNotAccessible(trans('entities.item-data'));
        }

        if (count($request->all()) === 0) {
            return Response::nothingToPatch();
        }

        $validator = (new ItemDataValidator())->update();

        if ($validator->fails()) {
            return Response::validationErrors($validator);
        }

        $invalid_fields = $this->checkForInvalidFields(
            array_keys(Config::get('api.item-data.validation-patch.fields'))
        );

        if (count($invalid_fields) > 0) {
            return Response::invalidFieldsInRequest($invalid_fields);
        }

        foreach ($request->only(['value']) as $model_key => $value) {
            $data->$model_key = $value;
        }

        try {
            $data->save();
        } catch (Exception $e) {
            return Response::failedToSaveModelForUpdate($e);
        }

        return Response::successNoContent();
    }
}
