<?php

namespace App\Http\Controllers\Action;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\HttpResponse\Response;
use App\Jobs\ClearCache;
use App\Models\PermittedUser;
use App\Models\ResourceType;
use App\HttpRequest\Validate\PermittedUser as PermittedUserValidator;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2023
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class PermittedUserController extends Controller
{
    public function create(Request $request, string $resource_type_id): JsonResponse
    {
        if ($this->hasWriteAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.resource-type'));
        }

        $resource_type = (new ResourceType())->single(
            $resource_type_id,
            $this->permitted_resource_types
        );

        $validator = (new PermittedUserValidator())->create([
            'resource_type_id' => $resource_type['resource_type_id']
        ]);

        if ($validator->fails()) {
            return \App\HttpResponse\Response::validationErrors($validator);
        }

        $cache_job_payload = (new \App\Cache\JobPayload())
            ->setGroupKey(\App\Cache\KeyGroup::PERMITTED_USER_CREATE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id
            ])
            ->setUserId($this->user_id);

        try {
            DB::transaction(function () use ($request, $resource_type_id) {
                $user = DB::table('users')
                    ->where('email', '=', $request->input('email'))
                    ->first();

                if ($user === null) {
                    throw new \RuntimeException('User cannot be found');
                }

                $permitted_user = new \App\Models\PermittedUser();
                $permitted_user->resource_type_id = $resource_type_id;
                $permitted_user->user_id = $user->id;
                $permitted_user->added_by = $this->user_id;

                if ($permitted_user->save() === true) {
                    $permitted_user_cache_job_payload = (new \App\Cache\JobPayload())
                        ->setGroupKey(\App\Cache\KeyGroup::RESOURCE_TYPE_CREATE)
                        ->setRouteParameters([])
                        ->setUserId($user->id);

                    ClearCache::dispatchSync($permitted_user_cache_job_payload->payload());
                } else {
                    throw new \RuntimeException('Unable to assign user or create clear cache request');
                }
            });

            ClearCache::dispatchSync($cache_job_payload->payload());
        } catch (Exception $e) {
            return Response::failedToSaveModelForCreate($e);
        }

        return Response::successNoContent();
    }

    public function delete(
        string $resource_type_id,
        string $permitted_user_id
    ): JsonResponse {
        if ($this->hasWriteAccessToResourceType((int) $resource_type_id) === false) {
            return \App\HttpResponse\Response::notFoundOrNotAccessible(trans('entities.permitted-user'));
        }

        $permitted_user = (new PermittedUser())->instance($resource_type_id, $permitted_user_id);

        if ($permitted_user === null) {
            return Response::failedToSelectModelForUpdateOrDelete();
        }

        $cache_job_payload = (new \App\Cache\JobPayload())
            ->setGroupKey(\App\Cache\KeyGroup::PERMITTED_USER_DELETE)
            ->setRouteParameters([])
            ->setUserId($this->user_id);

        try {
            DB::transaction(function () use ($permitted_user) {
                $permitted_user->delete();
            });

            ClearCache::dispatchSync($cache_job_payload->payload());

            return Response::successNoContent();
        } catch (QueryException $e) {
            return Response::foreignKeyConstraintError($e);
        } catch (Exception $e) {
            return Response::notFound(trans('entities.permitted-user'), $e);
        }
    }
}
