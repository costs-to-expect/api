<?php

namespace App\Http\Controllers;

use App\Jobs\ClearCache;
use App\Models\ResourceType;
use App\Request\Validate\PermittedUser as PermittedUserValidator;
use App\Response\Responses;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class PermittedUserManage extends Controller
{
    public function create(string $resource_type_id): JsonResponse
    {
        if ($this->writeAccessToResourceType((int) $resource_type_id) === false) {
            \App\Response\Responses::notFoundOrNotAccessible(trans('entities.resource-type'));
        }

        $resource_type = (new ResourceType())->single(
            $resource_type_id,
            $this->viewable_resource_types
        );

        $validator = (new PermittedUserValidator)->create([
            'resource_type_id' => $resource_type['resource_type_id']
        ]);

        if ($validator->fails()) {
            return \App\Request\BodyValidation::returnValidationErrors($validator);
        }

        $cache_job_payload = (new \App\Cache\JobPayload())
            ->setGroupKey(\App\Cache\KeyGroup::PERMITTED_USER_CREATE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id
            ])
            ->setPermittedUser($this->writeAccessToResourceType((int) $resource_type_id))
            ->setUserId($this->user_id);

        try {
            DB::transaction(function() use ($resource_type_id) {

                $user = DB::table('users')
                    ->where('email', '=', request()->input('email'))
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
                        ->setPermittedUser($this->writeAccessToResourceType((int)$resource_type_id))
                        ->setUserId($user->id);

                    ClearCache::dispatch($permitted_user_cache_job_payload->payload());
                } else {
                    throw new \RuntimeException('Unable to assign user or create clear cache request');
                }
            });

            ClearCache::dispatch($cache_job_payload->payload());

        } catch (Exception $e) {
            return Responses::failedToSaveModelForCreate();
        }

        return Responses::successNoContent();
    }
}