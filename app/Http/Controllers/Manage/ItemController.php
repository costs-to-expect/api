<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Models\ItemLog;
use Illuminate\Http\Request;
use App\Cache\JobPayload;
use App\Cache\KeyGroup;
use App\HttpRequest\Hash;
use App\HttpResponse\Response;
use App\ItemType\Select;
use App\Jobs\ClearCache;
use App\Models\Item;
use App\Models\ItemPartialTransfer;
use App\Models\ItemTransfer;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config as LaravelConfig;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemController extends Controller
{
    public function create(string $resource_type_id, string $resource_id): JsonResponse
    {
        if ($this->hasWriteAccessToResourceType((int) $resource_type_id) === false) {
            return Response::notFoundOrNotAccessible(trans('entities.resource'));
        }

        $item_type = Select::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense' => $this->createAllocatedExpense((int) $resource_type_id, (int) $resource_id),
            'budget' => $this->createBudget((int) $resource_type_id, (int) $resource_id),
            'game' => $this->createGame((int) $resource_type_id, (int) $resource_id),
            default => throw new \OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    private function createAllocatedExpense(int $resource_type_id, int $resource_id): JsonResponse
    {
        $request = request();

        $validator = $this->validateAllocatedExpenseForCreate();
        if ($validator !== null) {
            return \App\HttpResponse\Response::validationErrors($validator);
        }

        $cache_job_payload = (new JobPayload())
            ->setGroupKey(KeyGroup::ITEM_CREATE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id,
                'resource_id' => $resource_id
            ])
            ->setUserId($this->user_id);

        try {
            $item_type_instance = DB::transaction(function () use ($request, $resource_id) {
                $item_instance = new Item([
                    'resource_id' => $resource_id,
                    'created_by' => $this->user_id
                ]);
                $item_instance->save();

                $hash = new Hash();
                $currency_id = $hash->decode('currency', $request->input('currency_id'));

                $item_type_instance = new \App\ItemType\AllocatedExpense\Models\Item([
                    'item_id' => $item_instance->id,
                    'name' => $request->input('name'),
                    'description' => $request->input('description', null),
                    'effective_date' => $request->input('effective_date'),
                    'publish_after' => $request->input('publish_after', null),
                    'currency_id' => $currency_id,
                    'total' => $request->input('total'),
                    'percentage' => $request->input('percentage', 100),
                    'created_at' => Date::now(),
                    'updated_at' => null
                ]);

                $item_type_instance->setActualisedTotal(
                    $request->input('total'),
                    $request->input('percentage', 100)
                );

                $item_type_instance->save();

                return $item_type_instance;
            });

            ClearCache::dispatchSync($cache_job_payload->payload());
        } catch (Exception $e) {
            return Response::failedToSaveModelForCreate($e);
        }

        return response()->json(
            (new \App\ItemType\AllocatedExpense\Transformer\Item(
                (new \App\ItemType\AllocatedExpense\Models\Item())->instanceToArray($item_type_instance)
            )
            )->asArray(),
            201
        );
    }

    private function createBudget(int $resource_type_id, int $resource_id): JsonResponse
    {
        $request = request();

        $validator = $this->validateBudgetForCreate();
        if ($validator !== null) {
            return \App\HttpResponse\Response::validationErrors($validator);
        }

        $cache_job_payload = (new JobPayload())
            ->setGroupKey(KeyGroup::ITEM_CREATE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id,
                'resource_id' => $resource_id
            ])
            ->setUserId($this->user_id);

        try {
            $item_type_instance = DB::transaction(function () use ($request, $resource_id) {
                $item_instance = new Item([
                    'resource_id' => $resource_id,
                    'created_by' => $this->user_id
                ]);
                $item_instance->save();

                $hash = new Hash();
                $currency_id = $hash->decode('currency', $request->input('currency_id'));

                $item_type_instance = new \App\ItemType\Budget\Models\Item([
                    'item_id' => $item_instance->id,
                    'name' => $request->input('name'),
                    'account' => $request->input('account'),
                    'target_account' => $request->input('target_account'),
                    'description' => $request->input('description', null),
                    'amount' => $request->input('amount', null),
                    'currency_id' => $currency_id,
                    'category' => $request->input('category', null),
                    'start_date' => $request->input('start_date'),
                    'end_date' => $request->input('end_date'),
                    'disabled' => (bool) $request->input('disabled'),
                    'frequency' => $request->input('frequency'),
                    'created_at' => Date::now(),
                    'updated_at' => null
                ]);

                $item_type_instance->save();

                return $item_type_instance;
            });

            ClearCache::dispatchSync($cache_job_payload->payload());
        } catch (Exception $e) {
            return Response::failedToSaveModelForCreate($e);
        }

        return response()->json(
            (new \App\ItemType\Budget\Transformer\Item(
                (new \App\ItemType\Budget\Models\Item())->instanceToArray($item_type_instance)
            )
            )->asArray(),
            201
        );
    }

    private function createGame(int $resource_type_id, int $resource_id): JsonResponse
    {
        $request = request();

        $validator = $this->validateGameForCreate();
        if ($validator !== null) {
            return \App\HttpResponse\Response::validationErrors($validator);
        }

        $cache_job_payload = (new JobPayload())
            ->setGroupKey(KeyGroup::ITEM_CREATE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id,
                'resource_id' => $resource_id
            ])
            ->setUserId($this->user_id);

        try {
            $item_type_instance = DB::transaction(function () use ($request, $resource_id) {
                $item_instance = new Item([
                    'resource_id' => $resource_id,
                    'created_by' => $this->user_id
                ]);
                $item_instance->save();

                $item_type_instance = new \App\ItemType\Game\Models\Item([
                    'item_id' => $item_instance->id,
                    'name' => $request->input('name'),
                    'description' => $request->input('description', null),
                    'game' => "{\"turns\": []}",
                    'statistics' => "{\"turns\": 0, \"scores\": []}",
                    'created_at' => Date::now(),
                    'updated_at' => null
                ]);

                $item_type_instance->save();

                return $item_type_instance;
            });

            ClearCache::dispatchSync($cache_job_payload->payload());
        } catch (Exception $e) {
            return Response::failedToSaveModelForCreate($e);
        }

        return response()->json(
            (new \App\ItemType\Game\Transformer\Item(
                (new \App\ItemType\Game\Models\Item())->instanceToArray($item_type_instance)
            )
            )->asArray(),
            201
        );
    }

    public function update(Request $request, string $resource_type_id, string $resource_id, string $item_id): JsonResponse
    {
        if ($this->hasWriteAccessToResourceType((int) $resource_type_id) === false) {
            return Response::notFoundOrNotAccessible(trans('entities.item'));
        }

        if (count($request->all()) === 0) {
            return Response::nothingToPatch();
        }

        $item_type = Select::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense' => $this->updateAllocatedExpense((int) $resource_type_id, (int) $resource_id, (int) $item_id),
            'budget' => $this->updateBudget((int) $resource_type_id, (int) $resource_id, (int) $item_id),
            'game' => $this->updateGame((int) $resource_type_id, (int) $resource_id, (int) $item_id),
            default => throw new \OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    private function updateAllocatedExpense(
        int $resource_type_id,
        int $resource_id,
        int $item_id
    ): JsonResponse {

        $invalid_fields = $this->checkForInvalidFieldsForAllocatedExpense();
        if ($invalid_fields !== null) {
            return $invalid_fields;
        }

        $validator = $this->validateAllocatedExpenseForUpdate();
        if ($validator !== null) {
            return \App\HttpResponse\Response::validationErrors($validator);
        }

        $request = request();

        $cache_job_payload = (new JobPayload())
            ->setGroupKey(KeyGroup::ITEM_DELETE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id,
                'resource_id' => $resource_id
            ])
            ->setUserId($this->user_id);

        $item_instance = (new Item())->instance($resource_type_id, $resource_id, $item_id);
        $item_type_instance = (new \App\ItemType\AllocatedExpense\Models\Item())->instance($item_id);

        if ($item_instance === null || $item_type_instance === null) {
            return Response::failedToSelectModelForUpdateOrDelete();
        }

        try {
            $item_instance->updated_by = $this->user_id;
            $item_instance->updated_at = Date::now();

            DB::transaction(static function () use ($request, $item_instance, $item_type_instance) {
                $set_actualised = false;
                foreach ($request->all() as $key => $value) {
                    $item_type_instance->$key = $value;

                    if (in_array($key, ['total', 'percentage']) === true) {
                        $set_actualised = true;
                    }

                    if ($key === 'currency_id') {
                        $hash = new Hash();
                        $item_type_instance->$key = $hash->decode('currency', $request->input('currency_id'));
                    }
                }

                if ($set_actualised === true) {
                    $item_type_instance->setActualisedTotal($item_type_instance->total, $item_type_instance->percentage);
                }

                $item_type_instance->updated_at = Date::now();

                return $item_instance->save() && $item_type_instance->save();
            });

            ClearCache::dispatchSync($cache_job_payload->payload());
        } catch (Exception $e) {
            return Response::failedToSaveModelForUpdate($e);
        }

        return Response::successNoContent();
    }

    private function updateBudget(
        int $resource_type_id,
        int $resource_id,
        int $item_id
    ): JsonResponse {

        $invalid_fields = $this->checkForInvalidFieldsForBudget();
        if ($invalid_fields !== null) {
            return $invalid_fields;
        }

        $validator = $this->validateBudgetForUpdate();
        if ($validator !== null) {
            return \App\HttpResponse\Response::validationErrors($validator);
        }

        $request = request();

        $cache_job_payload = (new JobPayload())
            ->setGroupKey(KeyGroup::ITEM_DELETE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id,
                'resource_id' => $resource_id
            ])
            ->setUserId($this->user_id);

        $item_instance = (new Item())->instance($resource_type_id, $resource_id, $item_id);
        $item_type_instance = (new \App\ItemType\Budget\Models\Item())->instance($item_id);

        if ($item_instance === null || $item_type_instance === null) {
            return Response::failedToSelectModelForUpdateOrDelete();
        }

        try {
            $item_instance->updated_by = $this->user_id;
            $item_instance->updated_at = Date::now();

            DB::transaction(static function () use ($request, $item_instance, $item_type_instance) {
                foreach ($request->all() as $key => $value) {
                    $item_type_instance->$key = $value;
                }

                $item_type_instance->updated_at = Date::now();

                return $item_instance->save() && $item_type_instance->save();
            });

            ClearCache::dispatchSync($cache_job_payload->payload());
        } catch (Exception $e) {
            return Response::failedToSaveModelForUpdate($e);
        }

        return Response::successNoContent();
    }

    private function checkForInvalidFieldsForGame(): ?JsonResponse
    {
        $config_base_path = 'api.item-type-game';

        $invalid_fields = $this->checkForInvalidFields(
            array_keys(LaravelConfig::get($config_base_path . '.validation-patch.fields', []))
        );

        if (count($invalid_fields) > 0) {
            return Response::invalidFieldsInRequest($invalid_fields);
        }

        return null;
    }

    private function updateGame(
        int $resource_type_id,
        int $resource_id,
        int $item_id
    ): JsonResponse {
        $this->validateGameForUpdate();

        $request = request();

        $cache_job_payload = (new JobPayload())
            ->setGroupKey(KeyGroup::ITEM_DELETE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id,
                'resource_id' => $resource_id
            ])
            ->setUserId($this->user_id);

        $item_instance = (new Item())->instance($resource_type_id, $resource_id, $item_id);
        $item_type_instance = (new \App\ItemType\Game\Models\Item())->instance($item_id);

        if ($item_instance === null || $item_type_instance === null) {
            return Response::failedToSelectModelForUpdateOrDelete();
        }

        try {
            $item_instance->updated_by = $this->user_id;
            $item_instance->updated_at = Date::now();

            DB::transaction(static function () use ($request, $item_instance, $item_type_instance) {
                foreach ($request->all() as $key => $value) {
                    if ($key === 'winner_id') {
                        $key = 'winner';

                        if ($value !== null) {
                            $winner = (new Hash())->decode('category', $request->input('winner_id'));

                            $value = null;
                            if ($winner !== false) {
                                $value = $winner;
                            }
                        }
                    }

                    $item_type_instance->$key = $value;
                }

                $item_type_instance->updated_at = Date::now();

                return $item_instance->save() && $item_type_instance->save();
            });

            ClearCache::dispatchSync($cache_job_payload->payload());
        } catch (Exception $e) {
            return Response::failedToSaveModelForUpdate($e);
        }

        return Response::successNoContent();
    }

    public function delete(string $resource_type_id, string $resource_id, string $item_id): JsonResponse
    {
        if ($this->hasWriteAccessToResourceType((int) $resource_type_id) === false) {
            return Response::notFoundOrNotAccessible(trans('entities.item'));
        }

        $item_type = Select::itemType((int) $resource_type_id);

        return match ($item_type) {
            'allocated-expense' => $this->deleteAllocatedExpense((int) $resource_type_id, (int) $resource_id, (int) $item_id),
            'budget' => $this->deleteBudget((int) $resource_type_id, (int) $resource_id, (int) $item_id),
            'game' => $this->deleteGame((int) $resource_type_id, (int) $resource_id, (int) $item_id),
            default => throw new \OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    private function deleteAllocatedExpense(
        string $resource_type_id,
        string $resource_id,
        string $item_id
    ): JsonResponse {
        $cache_job_payload = (new JobPayload())
            ->setGroupKey(KeyGroup::ITEM_DELETE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id,
                'resource_id' => $resource_id
            ])
            ->setUserId($this->user_id);

        $item_model = new \App\ItemType\AllocatedExpense\Models\Item();

        $item_type_instance = $item_model->instance($item_id);
        $item_instance = (new Item())->instance($resource_type_id, $resource_id, $item_id);

        if ($item_instance === null || $item_type_instance === null) {
            return Response::notFound(trans('entities.item'));
        }

        if ($item_model->hasCategoryAssignments($item_id) === true) {
            return Response::foreignKeyConstraintCategory();
        }

        try {
            DB::transaction(static function () use ($item_id, $item_type_instance, $item_instance) {
                (new ItemLog())->deleteLogEntries($item_id);
                (new ItemTransfer())->deleteTransfers($item_id);
                (new ItemPartialTransfer())->deleteTransfers($item_id);
                $item_type_instance->delete();
                $item_instance->delete();
            });

            ClearCache::dispatchSync($cache_job_payload->payload());

            return Response::successNoContent();
        } catch (QueryException $e) {
            return Response::foreignKeyConstraintError($e);
        } catch (Exception $e) {
            return Response::notFound(trans('entities.item'), $e);
        }
    }

    private function deleteBudget(
        string $resource_type_id,
        string $resource_id,
        string $item_id
    ): JsonResponse {
        $cache_job_payload = (new JobPayload())
            ->setGroupKey(KeyGroup::ITEM_DELETE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id,
                'resource_id' => $resource_id
            ])
            ->setUserId($this->user_id);

        $item_model = new \App\ItemType\Budget\Models\Item();

        $item_type_instance = $item_model->instance($item_id);
        $item_instance = (new Item())->instance($resource_type_id, $resource_id, $item_id);

        if ($item_instance === null || $item_type_instance === null) {
            return Response::notFound(trans('entities.item'));
        }

        if ($item_model->hasCategoryAssignments($item_id) === true) {
            return Response::foreignKeyConstraintCategory();
        }

        try {
            DB::transaction(static function () use ($item_id, $item_type_instance, $item_instance) {
                $item_type_instance->delete();
                $item_instance->delete();
            });

            ClearCache::dispatchSync($cache_job_payload->payload());

            return Response::successNoContent();
        } catch (QueryException $e) {
            return Response::foreignKeyConstraintError($e);
        } catch (Exception $e) {
            return Response::notFound(trans('entities.item'), $e);
        }
    }

    private function deleteGame(
        string $resource_type_id,
        string $resource_id,
        string $item_id
    ): JsonResponse {
        $cache_job_payload = (new JobPayload())
            ->setGroupKey(KeyGroup::ITEM_DELETE)
            ->setRouteParameters([
                'resource_type_id' => $resource_type_id,
                'resource_id' => $resource_id
            ])
            ->setUserId($this->user_id);

        $item_model = new \App\ItemType\Game\Models\Item();

        $item_type_instance = $item_model->instance($item_id);
        $item_instance = (new Item())->instance($resource_type_id, $resource_id, $item_id);

        if ($item_instance === null || $item_type_instance === null) {
            return Response::notFound(trans('entities.item'));
        }

        if ($item_model->hasCategoryAssignments($item_id) === true) {
            return Response::foreignKeyConstraintCategory();
        }

        try {
            DB::transaction(static function () use ($item_id, $item_type_instance, $item_instance) {
                (new ItemLog())->deleteLogEntries($item_id);
                $item_type_instance->delete();
                $item_instance->delete();
            });

            ClearCache::dispatchSync($cache_job_payload->payload());

            return Response::successNoContent();
        } catch (QueryException $e) {
            return Response::foreignKeyConstraintError($e);
        } catch (Exception $e) {
            return Response::notFound(trans('entities.item'), $e);
        }
    }

    private function validateAllocatedExpenseForCreate(): ?\Illuminate\Validation\Validator
    {
        $request = request();

        $config_base_path = 'api.item-type-allocated-expense';

        $messages = [];
        foreach (LaravelConfig::get($config_base_path . '.validation-post.messages', []) as $key => $custom_message) {
            $messages[$key] = trans($custom_message);
        }

        $decode = $this->hash->currency()->decode($request->input('currency_id'));
        $currency_id = null;
        if (count($decode) === 1) {
            $currency_id = $decode[0];
        }

        $validator = ValidatorFacade::make(
            [
                ...$request->only([
                        'name',
                        'description',
                        'effective_date',
                        'publish_after',
                        'currency_id',
                        'total',
                        'percentage',
                    ]),
                ...['currency_id' => $currency_id]
            ],
            LaravelConfig::get($config_base_path . '.validation-post.fields', []),
            $messages
        );

        if ($validator->fails()) {
            return $validator;
        }

        return null;
    }

    private function checkForInvalidFieldsForAllocatedExpense(): ?JsonResponse
    {
        $config_base_path = 'api.item-type-allocated-expense';

        $invalid_fields = $this->checkForInvalidFields(
            array_keys(LaravelConfig::get($config_base_path . '.validation-patch.fields', []))
        );

        if (count($invalid_fields) > 0) {
            return Response::invalidFieldsInRequest($invalid_fields);
        }

        return null;
    }

    private function validateAllocatedExpenseForUpdate(): ?\Illuminate\Validation\Validator
    {
        $request = request();

        $config_base_path = 'api.item-type-allocated-expense';

        $merge_array = [];
        if (array_key_exists('currency_id', $request->all())) {
            $decode = $this->hash->currency()->decode($request->input('currency_id'));
            $currency_id = null;
            if (count($decode) === 1) {
                $currency_id = $decode[0];
            }

            $merge_array = ['currency_id' => $currency_id];
        }

        $messages = [];
        foreach (LaravelConfig::get($config_base_path . '.validation-patch.messages', []) as $key => $custom_message) {
            $messages[$key] = trans($custom_message);
        }

        $validator = ValidatorFacade::make(
            [
                ...$request->only([
                        'name',
                        'description',
                        'effective_date',
                        'publish_after',
                        'currency_id',
                        'total',
                        'percentage',
                   ]),
                ...$merge_array
            ],
            LaravelConfig::get($config_base_path . '.validation-patch.fields', []),
            $messages
        );

        if ($validator->fails()) {
            return $validator;
        }

        return null;
    }

    private function checkForInvalidFieldsForBudget(): ?JsonResponse
    {
        $config_base_path = 'api.item-type-budget';

        $invalid_fields = $this->checkForInvalidFields(
            array_keys(LaravelConfig::get($config_base_path . '.validation-patch.fields', []))
        );

        if (count($invalid_fields) > 0) {
            return Response::invalidFieldsInRequest($invalid_fields);
        }

        return null;
    }

    private function validateBudgetForUpdate(): ?\Illuminate\Validation\Validator
    {
        $request = request();

        $config_base_path = 'api.item-type-budget';

        $messages = [];
        foreach (LaravelConfig::get($config_base_path . '.validation-patch.messages', []) as $key => $custom_message) {
            $messages[$key] = trans($custom_message);
        }

        $validator = ValidatorFacade::make(
            $request->only([
                'name',
                'account',
                'target_account',
                'description',
                'amount',
                'category',
                'start_date',
                'end_date',
                'disabled',
                'frequency',
            ]),
            LaravelConfig::get($config_base_path . '.validation-patch.fields', []),
            $messages
        );

        if ($validator->fails()) {
            return $validator;
        }

        return null;
    }

    private function validateBudgetForCreate(): ?\Illuminate\Validation\Validator
    {
        $request = request();

        $config_base_path = 'api.item-type-budget';

        $messages = [];
        foreach (LaravelConfig::get($config_base_path . '.validation-post.messages', []) as $key => $custom_message) {
            $messages[$key] = trans($custom_message);
        }

        $decode = $this->hash->currency()->decode($request->input('currency_id'));
        $currency_id = null;
        if (count($decode) === 1) {
            $currency_id = $decode[0];
        }

        $validator = ValidatorFacade::make(
            [
                ...$request->only([
                        'name',
                        'account',
                        'target_account',
                        'description',
                        'amount',
                        'currency_id',
                        'category',
                        'start_date',
                        'end_date',
                        'disabled',
                        'frequency',
                    ]),
                ...['currency_id' => $currency_id]
            ],
            LaravelConfig::get($config_base_path . '.validation-post.fields', []),
            $messages
        );

        if ($validator->fails()) {
            return $validator;
        }

        return null;
    }

    private function validateGameForCreate(): ?\Illuminate\Validation\Validator
    {
        $request = request();

        $config_base_path = 'api.item-type-game';

        $messages = [];
        foreach (LaravelConfig::get($config_base_path . '.validation-post.messages', []) as $key => $custom_message) {
            $messages[$key] = trans($custom_message);
        }

        $validator = ValidatorFacade::make(
            $request->only([
                'name',
                'description',
            ]),
            LaravelConfig::get($config_base_path . '.validation-post.fields', []),
            $messages
        );

        if ($validator->fails()) {
            return $validator;
        }

        return null;
    }

    private function validateGameForUpdate(): ?\Illuminate\Validation\Validator
    {
        $request = request();

        $config_base_path = 'api.item-type-game';

        $merge_array = [];
        if (array_key_exists('winner_id', $request->all())) {
            $decode = $this->hash->category()->decode($request->input('winner_id'));
            $winner_id = null;
            if (count($decode) === 1) {
                $winner_id = $decode[0];
            }

            $merge_array = ['winner_id' => $winner_id];
        }

        $messages = [];
        foreach (LaravelConfig::get($config_base_path . '.validation-patch.messages', []) as $key => $custom_message) {
            $messages[$key] = trans($custom_message);
        }

        $validator = ValidatorFacade::make(
            [
                ...$request->only([
                    'game',
                    'statistics',
                    'winner_id',
                    'score',
                    'complete',
                ]),
                ...$merge_array
            ],
            LaravelConfig::get($config_base_path . '.validation-patch.fields', []),
            $messages
        );

        if ($validator->fails()) {
            return $validator;
        }

        return null;
    }
}
