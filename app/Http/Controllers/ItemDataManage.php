<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\HttpResponse\Response;
use App\Jobs\ClearCache;
use App\Models\Resource;
use App\Models\ResourceItemSubtype;
use App\HttpRequest\Validate\Resource as ResourceValidator;
use App\Transformer\Resource as ResourceTransformer;
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
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.resource-type'));
        }

        $validator = (new ResourceValidator())->create([
            'resource_type_id' => $resource_type_id,
            'item_type_id' => $resource_type['resource_type_item_type_id']
        ]);

        if ($validator->fails()) {
            return \App\HttpResponse\Response::validationErrors($validator);
        }

        $cache_job_payload = (new \App\Cache\JobPayload())
            ->setGroupKey(\App\Cache\KeyGroup::RESOURCE_CREATE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id
            ])
            ->isPermittedUser($this->hasWriteAccessToResourceType((int) $resource_type_id))
            ->setUserId($this->user_id);

        try {
            $resource = DB::transaction(function () use ($request, $resource_type_id) {
                $resource = new Resource([
                    'resource_type_id' => $resource_type_id,
                    'name' => $request->input('name'),
                    'description' => $request->input('description'),
                    'data' => $request->input('data')
                ]);
                $resource->save();

                $item_subtype_id = $this->hash->decode('item-subtype', $request->input('item_subtype_id'));

                if ($item_subtype_id === false) {
                    return \App\HttpResponse\Response::unableToDecode();
                }

                $resource_item_subtype = new ResourceItemSubtype([
                    'resource_id' => $resource->id,
                    'item_subtype_id' => $item_subtype_id
                ]);
                $resource_item_subtype->save();

                return $resource;
            });

            ClearCache::dispatch($cache_job_payload->payload());
        } catch (Exception $e) {
            return Response::failedToSaveModelForCreate($e);
        }

        return response()->json(
            (new ResourceTransformer((new Resource())->instanceToArray($resource)))->asArray(),
            201
        );
    }
}
