<?php

namespace App\Http\Controllers;

use App\Cache\JobPayload;
use App\Cache\KeyGroup;
use App\ItemType\Entity;
use App\Jobs\ClearCache;
use App\Models\Item;
use App\Models\ItemTransfer;
use App\Response\Responses;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config as LaravelConfig;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemManage extends Controller
{
    public function create(string $resource_type_id, string $resource_id): JsonResponse
    {
        if ($this->writeAccessToResourceType((int) $resource_type_id) === false) {
            Responses::notFoundOrNotAccessible(trans('entities.resource'));
        }

        $item_type = Entity::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense' => $this->createAllocatedExpense((int) $resource_type_id, (int) $resource_id),
            'simple-expense' => $this->createSimpleExpense((int) $resource_type_id, (int) $resource_id),
            'simple-item' => $this->createSimpleItem((int) $resource_type_id, (int) $resource_id),
            'game' => $this->createGame((int) $resource_type_id, (int) $resource_id),
            default => throw new \OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    private function createAllocatedExpense(int $resource_type_id, int $resource_id): JsonResponse
    {
        $config_base_path = 'api.item-type-allocated-expense';

        $messages = [];
        foreach (LaravelConfig::get($config_base_path . '.validation.POST.messages', []) as $key => $custom_message) {
            $messages[$key] = trans($custom_message);
        }

        $decode = $this->hash->currency()->decode(request()->input('currency_id'));
        $currency_id = null;
        if (count($decode) === 1) {
            $currency_id = $decode[0];
        }

        $validator = ValidatorFacade::make(
            array_merge(
                request()->all(),
                ['currency_id' => $currency_id]
            ),
            LaravelConfig::get($config_base_path . '.validation.POST.fields', []),
            $messages
        );

        if ($validator->fails()) {
            return \App\Request\BodyValidation::returnValidationErrors($validator);
        }

        $item = new \App\ItemType\AllocatedExpense\Item();

        $cache_job_payload = (new JobPayload())
            ->setGroupKey(KeyGroup::ITEM_CREATE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id,
                'resource_id' => $resource_id
            ])
            ->setPermittedUser($this->writeAccessToResourceType($resource_type_id))
            ->setUserId($this->user_id);

        try {
            [$item_instance, $item_type_instance] = DB::transaction(function() use ($resource_id, $item) {
                $item_instance = new Item([
                    'resource_id' => $resource_id,
                    'created_by' => $this->user_id
                ]);
                $item_instance->save();
                $item_type_instance = $item->create($item_instance->id);

                return [$item_instance, $item_type_instance];
            });

            ClearCache::dispatch($cache_job_payload->payload());

        } catch (Exception $e) {
            return Responses::failedToSaveModelForCreate();
        }

        $model = new \App\ItemType\AllocatedExpense\Models\Item();
        return response()->json(
            $item->transformer($model->instanceToArray($item_instance, $item_type_instance))->asArray(),
            201
        );
    }

    private function createGame(int $resource_type_id, int $resource_id): JsonResponse
    {
        $config_base_path = 'api.item-type-game';

        $messages = [];
        foreach (LaravelConfig::get($config_base_path . '.validation.POST.messages', []) as $key => $custom_message) {
            $messages[$key] = trans($custom_message);
        }

        $validator = ValidatorFacade::make(
            request()->all(),
            LaravelConfig::get($config_base_path . '.validation.POST.fields', []),
            $messages
        );

        if ($validator->fails()) {
            return \App\Request\BodyValidation::returnValidationErrors($validator);
        }

        $item = new \App\ItemType\Game\Item();

        $cache_job_payload = (new JobPayload())
            ->setGroupKey(KeyGroup::ITEM_CREATE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id,
                'resource_id' => $resource_id
            ])
            ->setPermittedUser($this->writeAccessToResourceType($resource_type_id))
            ->setUserId($this->user_id);

        try {
            [$item_instance, $item_type_instance] = DB::transaction(function() use ($resource_id, $item) {
                $item_instance = new Item([
                    'resource_id' => $resource_id,
                    'created_by' => $this->user_id
                ]);
                $item_instance->save();
                $item_type_instance = $item->create($item_instance->id);

                return [$item_instance, $item_type_instance];
            });

            ClearCache::dispatch($cache_job_payload->payload());

        } catch (Exception $e) {
            return Responses::failedToSaveModelForCreate();
        }

        $model = new \App\ItemType\Game\Models\Item();
        return response()->json(
            $item->transformer($model->instanceToArray($item_instance, $item_type_instance))->asArray(),
            201
        );
    }

    private function createSimpleExpense(int $resource_type_id, int $resource_id): JsonResponse
    {
        $config_base_path = 'api.item-type-simple-expense';

        $messages = [];
        foreach (LaravelConfig::get($config_base_path . '.validation.POST.messages', []) as $key => $custom_message) {
            $messages[$key] = trans($custom_message);
        }

        $decode = $this->hash->currency()->decode(request()->input('currency_id'));
        $currency_id = null;
        if (count($decode) === 1) {
            $currency_id = $decode[0];
        }

        $validator = ValidatorFacade::make(
            array_merge(
                request()->all(),
                ['currency_id' => $currency_id]
            ),
            LaravelConfig::get($config_base_path . '.validation.POST.fields', []),
            $messages
        );

        if ($validator->fails()) {
            return \App\Request\BodyValidation::returnValidationErrors($validator);
        }

        $item = new \App\ItemType\SimpleExpense\Item();

        $cache_job_payload = (new JobPayload())
            ->setGroupKey(KeyGroup::ITEM_CREATE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id,
                'resource_id' => $resource_id
            ])
            ->setPermittedUser($this->writeAccessToResourceType($resource_type_id))
            ->setUserId($this->user_id);

        try {
            [$item_instance, $item_type_instance] = DB::transaction(function() use ($resource_id, $item) {
                $item_instance = new Item([
                    'resource_id' => $resource_id,
                    'created_by' => $this->user_id
                ]);
                $item_instance->save();
                $item_type_instance = $item->create($item_instance->id);

                return [$item_instance, $item_type_instance];
            });

            ClearCache::dispatch($cache_job_payload->payload());

        } catch (Exception $e) {
            return Responses::failedToSaveModelForCreate();
        }

        $model = new \App\ItemType\SimpleExpense\Models\Item();
        return response()->json(
            $item->transformer($model->instanceToArray($item_instance, $item_type_instance))->asArray(),
            201
        );
    }

    private function createSimpleItem(int $resource_type_id, int $resource_id): JsonResponse
    {
        $config_base_path = 'api.item-type-simple-item';

        $messages = [];
        foreach (LaravelConfig::get($config_base_path . '.validation.POST.messages', []) as $key => $custom_message) {
            $messages[$key] = trans($custom_message);
        }

        $validator = ValidatorFacade::make(
            request()->all(),
            LaravelConfig::get($config_base_path . '.validation.POST.fields', []),
            $messages
        );

        if ($validator->fails()) {
            return \App\Request\BodyValidation::returnValidationErrors($validator);
        }

        $item = new \App\ItemType\SimpleItem\Item();

        $cache_job_payload = (new JobPayload())
            ->setGroupKey(KeyGroup::ITEM_CREATE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id,
                'resource_id' => $resource_id
            ])
            ->setPermittedUser($this->writeAccessToResourceType($resource_type_id))
            ->setUserId($this->user_id);

        try {
            [$item_instance, $item_type_instance] = DB::transaction(function() use ($resource_id, $item) {
                $item_instance = new Item([
                    'resource_id' => $resource_id,
                    'created_by' => $this->user_id
                ]);
                $item_instance->save();
                $item_type_instance = $item->create($item_instance->id);

                return [$item_instance, $item_type_instance];
            });

            ClearCache::dispatch($cache_job_payload->payload());

        } catch (Exception $e) {
            return Responses::failedToSaveModelForCreate();
        }

        $model = new \App\ItemType\SimpleItem\Models\Item();
        return response()->json(
            $item->transformer($model->instanceToArray($item_instance, $item_type_instance))->asArray(),
            201
        );
    }

    public function update(string $resource_type_id, string $resource_id,string $item_id): JsonResponse
    {
        if ($this->writeAccessToResourceType((int) $resource_type_id) === false) {
            Responses::notFoundOrNotAccessible(trans('entities.item'));
        }

        $item_type = Entity::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense' => $this->updateAllocatedExpense((int) $resource_type_id, (int) $resource_id, (int) $item_id),
            'game' => $this->updateGame((int) $resource_type_id, (int) $resource_id, (int) $item_id),
            'simple-expense' => $this->updateSimpleExpense((int) $resource_type_id, (int) $resource_id, (int) $item_id),
            'simple-item' => $this->updateSimpleItem((int) $resource_type_id, (int) $resource_id, (int) $item_id),
            default => throw new \OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    private function updateAllocatedExpense(int $resource_type_id, int $resource_id, int $item_id): JsonResponse
    {
        $config_base_path = 'api.item-type-allocated-expense';

        if (count(request()->all()) === 0) {
            return Responses::nothingToPatch();
        }

        $invalid_fields = \App\Request\BodyValidation::checkForInvalidFields(
            array_keys(LaravelConfig::get($config_base_path . '.validation.PATCH.fields', []))
        );

        if (count($invalid_fields) > 0) {
            return Responses::invalidFieldsInRequest($invalid_fields);
        }

        $merge_array = [];
        if (array_key_exists('currency_id', request()->all())) {
            $decode = $this->hash->currency()->decode(request()->input('currency_id'));
            $currency_id = null;
            if (count($decode) === 1) {
                $currency_id = $decode[0];
            }

            $merge_array = ['currency_id' => $currency_id];
        }

        $messages = [];
        foreach (LaravelConfig::get($config_base_path . '.validation.PATCH.messages', []) as $key => $custom_message) {
            $messages[$key] = trans($custom_message);
        }

        $validator = ValidatorFacade::make(
            array_merge(
                request()->all(),
                $merge_array
            ),
            LaravelConfig::get($config_base_path . '.validation.PATCH.fields', []),
            $messages
        );

        if ($validator->fails()) {
            return \App\Request\BodyValidation::returnValidationErrors($validator);
        }

        $cache_job_payload = (new JobPayload())
            ->setGroupKey(KeyGroup::ITEM_DELETE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id,
                'resource_id' => $resource_id
            ])
            ->setPermittedUser($this->writeAccessToResourceType($resource_type_id))
            ->setUserId($this->user_id);

        $item_instance = (new Item())->instance($resource_type_id, $resource_id, $item_id);
        $item_type_instance = (new \App\ItemType\AllocatedExpense\Models\Item())->instance($item_id);

        if ($item_instance === null || $item_type_instance === null) {
            return Responses::failedToSelectModelForUpdateOrDelete();
        }

        try {
            $item_instance->updated_by = $this->user_id;

            DB::transaction(static function() use ($item_instance, $item_type_instance) {
                if ($item_instance->save() === true) {
                    $allocated_expense = new \App\ItemType\AllocatedExpense\Item();
                    $allocated_expense->update(request()->all(), $item_type_instance);
                }
            });

            ClearCache::dispatch($cache_job_payload->payload());

        } catch (Exception $e) {
            return Responses::failedToSaveModelForUpdate();
        }

        return Responses::successNoContent();
    }

    private function updateGame(int $resource_type_id, int $resource_id, int $item_id): JsonResponse
    {
        $config_base_path = 'api.item-type-game';

        if (count(request()->all()) === 0) {
            return Responses::nothingToPatch();
        }

        $invalid_fields = \App\Request\BodyValidation::checkForInvalidFields(
            array_keys(LaravelConfig::get($config_base_path . '.validation.PATCH.fields', []))
        );

        if (count($invalid_fields) > 0) {
            return Responses::invalidFieldsInRequest($invalid_fields);
        }

        $messages = [];
        foreach (LaravelConfig::get($config_base_path . '.validation.PATCH.messages', []) as $key => $custom_message) {
            $messages[$key] = trans($custom_message);
        }

        $validator = ValidatorFacade::make(
            request()->all(),
            LaravelConfig::get($config_base_path . '.validation.PATCH.fields', []),
            $messages
        );

        if ($validator->fails()) {
            return \App\Request\BodyValidation::returnValidationErrors($validator);
        }

        $cache_job_payload = (new JobPayload())
            ->setGroupKey(KeyGroup::ITEM_DELETE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id,
                'resource_id' => $resource_id
            ])
            ->setPermittedUser($this->writeAccessToResourceType($resource_type_id))
            ->setUserId($this->user_id);

        $item_instance = (new Item())->instance($resource_type_id, $resource_id, $item_id);
        $item_type_instance = (new \App\ItemType\Game\Models\Item())->instance($item_id);

        if ($item_instance === null || $item_type_instance === null) {
            return Responses::failedToSelectModelForUpdateOrDelete();
        }

        try {
            $item_instance->updated_by = $this->user_id;

            DB::transaction(static function() use ($item_instance, $item_type_instance) {
                if ($item_instance->save() === true) {
                    $allocated_expense = new \App\ItemType\Game\Item();
                    $allocated_expense->update(request()->all(), $item_type_instance);
                }
            });

            ClearCache::dispatch($cache_job_payload->payload());

        } catch (Exception $e) {
            return Responses::failedToSaveModelForUpdate();
        }

        return Responses::successNoContent();
    }

    private function updateSimpleExpense(int $resource_type_id, int $resource_id, int $item_id): JsonResponse
    {
        $config_base_path = 'api.item-type-simple-expense';

        if (count(request()->all()) === 0) {
            return Responses::nothingToPatch();
        }

        $invalid_fields = \App\Request\BodyValidation::checkForInvalidFields(
            array_keys(LaravelConfig::get($config_base_path . '.validation.PATCH.fields', []))
        );

        if (count($invalid_fields) > 0) {
            return Responses::invalidFieldsInRequest($invalid_fields);
        }

        $merge_array = [];
        if (array_key_exists('currency_id', request()->all())) {
            $decode = $this->hash->currency()->decode(request()->input('currency_id'));
            $currency_id = null;
            if (count($decode) === 1) {
                $currency_id = $decode[0];
            }

            $merge_array = ['currency_id' => $currency_id];
        }

        $messages = [];
        foreach (LaravelConfig::get($config_base_path . '.validation.PATCH.messages', []) as $key => $custom_message) {
            $messages[$key] = trans($custom_message);
        }

        $validator = ValidatorFacade::make(
            array_merge(
                request()->all(),
                $merge_array
            ),
            LaravelConfig::get($config_base_path . '.validation.PATCH.fields', []),
            $messages
        );

        if ($validator->fails()) {
            return \App\Request\BodyValidation::returnValidationErrors($validator);
        }

        $cache_job_payload = (new JobPayload())
            ->setGroupKey(KeyGroup::ITEM_DELETE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id,
                'resource_id' => $resource_id
            ])
            ->setPermittedUser($this->writeAccessToResourceType($resource_type_id))
            ->setUserId($this->user_id);

        $item_instance = (new Item())->instance($resource_type_id, $resource_id, $item_id);
        $item_type_instance = (new \App\ItemType\SimpleExpense\Models\Item())->instance($item_id);

        if ($item_instance === null || $item_type_instance === null) {
            return Responses::failedToSelectModelForUpdateOrDelete();
        }

        try {
            $item_instance->updated_by = $this->user_id;

            DB::transaction(static function() use ($item_instance, $item_type_instance) {
                if ($item_instance->save() === true) {
                    $allocated_expense = new \App\ItemType\SimpleExpense\Item();
                    $allocated_expense->update(request()->all(), $item_type_instance);
                }
            });

            ClearCache::dispatch($cache_job_payload->payload());

        } catch (Exception $e) {
            return Responses::failedToSaveModelForUpdate();
        }

        return Responses::successNoContent();
    }

    private function updateSimpleItem(int $resource_type_id, int $resource_id, int $item_id): JsonResponse
    {
        $config_base_path = 'api.item-type-simple-item';

        if (count(request()->all()) === 0) {
            return Responses::nothingToPatch();
        }

        $invalid_fields = \App\Request\BodyValidation::checkForInvalidFields(
            array_keys(LaravelConfig::get($config_base_path . '.validation.PATCH.fields', []))
        );

        if (count($invalid_fields) > 0) {
            return Responses::invalidFieldsInRequest($invalid_fields);
        }

        $messages = [];
        foreach (LaravelConfig::get($config_base_path . '.validation.PATCH.messages', []) as $key => $custom_message) {
            $messages[$key] = trans($custom_message);
        }

        $validator = ValidatorFacade::make(
            request()->all(),
            LaravelConfig::get($config_base_path . '.validation.PATCH.fields', []),
            $messages
        );

        if ($validator->fails()) {
            return \App\Request\BodyValidation::returnValidationErrors($validator);
        }

        $cache_job_payload = (new JobPayload())
            ->setGroupKey(KeyGroup::ITEM_DELETE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id,
                'resource_id' => $resource_id
            ])
            ->setPermittedUser($this->writeAccessToResourceType($resource_type_id))
            ->setUserId($this->user_id);

        $item_instance = (new Item())->instance($resource_type_id, $resource_id, $item_id);
        $item_type_instance = (new \App\ItemType\SimpleItem\Models\Item())->instance($item_id);

        if ($item_instance === null || $item_type_instance === null) {
            return Responses::failedToSelectModelForUpdateOrDelete();
        }

        try {
            $item_instance->updated_by = $this->user_id;

            DB::transaction(static function() use ($item_instance, $item_type_instance) {
                if ($item_instance->save() === true) {
                    $allocated_expense = new \App\ItemType\SimpleItem\Item();
                    $allocated_expense->update(request()->all(), $item_type_instance);
                }
            });

            ClearCache::dispatch($cache_job_payload->payload());

        } catch (Exception $e) {
            return Responses::failedToSaveModelForUpdate();
        }

        return Responses::successNoContent();
    }

    public function delete(string $resource_type_id, string $resource_id,string $item_id): JsonResponse
    {
        if ($this->writeAccessToResourceType((int) $resource_type_id) === false) {
            Responses::notFoundOrNotAccessible(trans('entities.item'));
        }

        $item_type = Entity::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense' => $this->deleteAllocatedExpense((int) $resource_type_id, (int) $resource_id, (int) $item_id),
            'game' => $this->deleteGame((int) $resource_type_id, (int) $resource_id, (int) $item_id),
            'simple-expense' => $this->deleteSimpleExpense((int) $resource_type_id, (int) $resource_id, (int) $item_id),
            'simple-item' => $this->deleteSimpleItem((int) $resource_type_id, (int) $resource_id, (int) $item_id),
            default => throw new \OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    private function deleteAllocatedExpense(
        string $resource_type_id,
        string $resource_id,
        string $item_id
    ): JsonResponse
    {
        $cache_job_payload = (new JobPayload())
            ->setGroupKey(KeyGroup::ITEM_DELETE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id,
                'resource_id' => $resource_id
            ])
            ->setPermittedUser($this->writeAccessToResourceType((int) $resource_type_id))
            ->setUserId($this->user_id);

        $item_model = new \App\ItemType\AllocatedExpense\Models\Item();

        $item_type_instance = $item_model->instance($item_id);
        $item_instance = (new Item())->instance($resource_type_id, $resource_id, $item_id);

        if ($item_instance === null || $item_type_instance === null) {
            return Responses::notFound(trans('entities.item'));
        }

        if ($item_model->hasCategoryAssignments($item_id) === true) {
            return Responses::foreignKeyConstraintError();
        }

        try {
            DB::transaction(static function() use ($item_id, $item_type_instance, $item_instance) {
                (new ItemTransfer())->deleteTransfers($item_id);
                $item_type_instance->delete();
                $item_instance->delete();
            });

            ClearCache::dispatch($cache_job_payload->payload());

            return Responses::successNoContent();
        } catch (QueryException $e) {
            return Responses::foreignKeyConstraintError();
        } catch (Exception $e) {
            return Responses::notFound(trans('entities.item'));
        }
    }

    private function deleteGame(
        string $resource_type_id,
        string $resource_id,
        string $item_id
    ): JsonResponse
    {
        $cache_job_payload = (new JobPayload())
            ->setGroupKey(KeyGroup::ITEM_DELETE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id,
                'resource_id' => $resource_id
            ])
            ->setPermittedUser($this->writeAccessToResourceType((int) $resource_type_id))
            ->setUserId($this->user_id);

        $item_model = new \App\ItemType\Game\Models\Item();

        $item_type_instance = $item_model->instance($item_id);
        $item_instance = (new Item())->instance($resource_type_id, $resource_id, $item_id);

        if ($item_instance === null || $item_type_instance === null) {
            return Responses::notFound(trans('entities.item'));
        }

        if ($item_model->hasCategoryAssignments($item_id) === true) {
            return Responses::foreignKeyConstraintError();
        }

        try {
            DB::transaction(static function() use ($item_id, $item_type_instance, $item_instance) {
                (new ItemTransfer())->deleteTransfers($item_id);
                $item_type_instance->delete();
                $item_instance->delete();
            });

            ClearCache::dispatch($cache_job_payload->payload());

            return Responses::successNoContent();
        } catch (QueryException $e) {
            return Responses::foreignKeyConstraintError();
        } catch (Exception $e) {
            return Responses::notFound(trans('entities.item'));
        }
    }

    private function deleteSimpleExpense(
        string $resource_type_id,
        string $resource_id,
        string $item_id
    ): JsonResponse
    {
        $cache_job_payload = (new JobPayload())
            ->setGroupKey(KeyGroup::ITEM_DELETE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id,
                'resource_id' => $resource_id
            ])
            ->setPermittedUser($this->writeAccessToResourceType((int) $resource_type_id))
            ->setUserId($this->user_id);

        $item_model = new \App\ItemType\SimpleExpense\Models\Item();

        $item_type_instance = $item_model->instance($item_id);
        $item_instance = (new Item())->instance($resource_type_id, $resource_id, $item_id);

        if ($item_instance === null || $item_type_instance === null) {
            return Responses::notFound(trans('entities.item'));
        }

        if ($item_model->hasCategoryAssignments($item_id) === true) {
            return Responses::foreignKeyConstraintError();
        }

        try {
            DB::transaction(static function() use ($item_id, $item_type_instance, $item_instance) {
                (new ItemTransfer())->deleteTransfers($item_id);
                $item_type_instance->delete();
                $item_instance->delete();
            });

            ClearCache::dispatch($cache_job_payload->payload());

            return Responses::successNoContent();
        } catch (QueryException $e) {
            return Responses::foreignKeyConstraintError();
        } catch (Exception $e) {
            return Responses::notFound(trans('entities.item'));
        }
    }

    private function deleteSimpleItem(
        string $resource_type_id,
        string $resource_id,
        string $item_id
    ): JsonResponse
    {
        $cache_job_payload = (new JobPayload())
            ->setGroupKey(KeyGroup::ITEM_DELETE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id,
                'resource_id' => $resource_id
            ])
            ->setPermittedUser($this->writeAccessToResourceType((int) $resource_type_id))
            ->setUserId($this->user_id);

        $item_model = new \App\ItemType\SimpleItem\Models\Item();

        $item_type_instance = $item_model->instance($item_id);
        $item_instance = (new Item())->instance($resource_type_id, $resource_id, $item_id);

        if ($item_instance === null || $item_type_instance === null) {
            return Responses::notFound(trans('entities.item'));
        }

        if ($item_model->hasCategoryAssignments($item_id) === true) {
            return Responses::foreignKeyConstraintError();
        }

        try {
            DB::transaction(static function() use ($item_id, $item_type_instance, $item_instance) {
                (new ItemTransfer())->deleteTransfers($item_id);
                $item_type_instance->delete();
                $item_instance->delete();
            });

            ClearCache::dispatch($cache_job_payload->payload());

            return Responses::successNoContent();
        } catch (QueryException $e) {
            return Responses::foreignKeyConstraintError();
        } catch (Exception $e) {
            return Responses::notFound(trans('entities.item'));
        }
    }
}
