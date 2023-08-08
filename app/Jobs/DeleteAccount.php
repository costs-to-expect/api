<?php

namespace App\Jobs;

use App\ItemType\Select;
use App\Models\Permission;
use App\Models\PermittedUser;
use App\Models\Resource;
use App\Notifications\FailedJob;
use App\Notifications\ResourceDeleted;
use App\Notifications\ResourceTypeDeleted;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Throwable;

class DeleteAccount implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected int $user_id;

    protected array $deletes;

    public int $tries = 1;

    public function __construct(int $user_id)
    {
        $this->user_id = $user_id;

        $this->deletes = [];
    }

    public function handle()
    {
        $resource_types = (new Permission())->permittedResourceTypesForUser($this->user_id);

        foreach ($resource_types as $resource_type_id) {
            /*TODO This is confusing to read as you are actually fetching number of permitted users excluding the given user*/
            $permitted_users = (new Permission())->permittedUsersForResourceType($resource_type_id, $this->user_id);
            if (count($permitted_users) === 0) {
                $resources = (new Resource())->paginatedCollection($resource_type_id);

                foreach ($resources as $resource) {
                    $this->deleteResourceAndAllData($resource_type_id, $resource['resource_id']);
                }

                $this->deletes = [];

                try {
                    DB::transaction(function () use ($resource_type_id) {
                        $this->deletes['subcategories'] = $this->deleteSubcategories($resource_type_id);
                        $this->deletes['categories'] = $this->deleteCategories($resource_type_id);
                        $this->deletes['resource-type-item-type'] = $this->deleteResourceTypeItemType($resource_type_id);
                        $this->deletes['permitted-users'] = $this->deletePermittedUsers($resource_type_id);
                        $this->deletes['resource-type'] = $this->deleteResourceType($resource_type_id);
                    });


                } catch (Throwable $e) {
                    throw new \Exception($e->getMessage());
                }

                Notification::route('mail', Config::get('api.app.config.admin_email'))
                    ->notify(new ResourceTypeDeleted($this->deletes));
            }

            if (count($permitted_users) > 0) {
                // Remove references to the user in the `permitted_user` table
                DB::update('UPDATE `permitted_user` SET `added_by` = ? WHERE `added_by` = ?', [$permitted_users[0], $this->user_id]);

                // Remove references to the user in the `item` table
                DB::update('UPDATE `item` SET `created_by` = ? WHERE `created_by` = ?', [$permitted_users[0], $this->user_id]);
                DB::update('UPDATE `item` SET `updated_by` = ? WHERE `updated_by` = ?', [$permitted_users[0], $this->user_id]);

                $permitted_user = (new PermittedUser())->instance($resource_type_id, $this->user_id);
                $permitted_user?->delete();
            }

            $cache_clear_payload = (new \App\Cache\JobPayload())
                ->setGroupKey(\App\Cache\KeyGroup::RESOURCE_TYPE_DELETE)
                ->setRouteParameters([
                    'resource_type_id' => $resource_type_id
                ])
                ->setUserId($this->user_id);

            ClearCache::dispatchSync($cache_clear_payload->payload());
        }

        $user = (new User())->instance($this->user_id);
        $user?->delete();
    }

    public function failed(Throwable $exception)
    {
        Notification::route('mail', Config::get('api.app.config.admin_email'))
            ->notify(new FailedJob([
                    'message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'trace' => $exception->getTraceAsString()
                ])
            );
    }

    protected function deleteAllocatedExpenseItems(int $resource_id): int
    {
        return DB::delete('
            DELETE FROM
                `item_type_allocated_expense`
            WHERE
                `item_type_allocated_expense`.`item_id` IN (
                SELECT `item`.`id` FROM `item` WHERE `item`.`resource_id` = ?
            )
        ', [$resource_id]);
    }

    protected function deleteBudgetItems(int $resource_id): int
    {
        return DB::delete('
            DELETE FROM
                `item_type_budget`
            WHERE
                `item_type_budget`.`item_id` IN (
                SELECT `item`.`id` FROM `item` WHERE `item`.`resource_id` = ?
            )
        ', [$resource_id]);
    }

    protected function deleteBudgetProItems(int $resource_id): int
    {
        return DB::delete('
            DELETE FROM
                `item_type_budget_pro`
            WHERE
                `item_type_budget_pro`.`item_id` IN (
                SELECT `item`.`id` FROM `item` WHERE `item`.`resource_id` = ?
            )
        ', [$resource_id]);
    }

    protected function deleteCategories(int $resource_type_id): int
    {
        return DB::delete('
            DELETE FROM
                `category`
            WHERE
                `category`.`resource_type_id` = ?
        ', [$resource_type_id]);
    }

    protected function deleteGameItems(int $resource_id): int
    {
        return DB::delete('
            DELETE FROM
                `item_type_game`
            WHERE
                `item_type_game`.`item_id` IN (
                SELECT `item`.`id` FROM `item` WHERE `item`.`resource_id` = ?
            )
        ', [$resource_id]);
    }

    protected function deleteItemCategories(int $resource_id): int
    {
        return DB::delete('
            DELETE FROM 
                `item_category`
            WHERE `item_category`.`item_id` IN (
                SELECT `item`.`id` FROM `item` WHERE `item`.`resource_id` = ?
            )
        ', [$resource_id]);
    }

    protected function deleteItemData(int $resource_id): int
    {
        return DB::delete('
            DELETE FROM 
                `item_data`
            WHERE
                `item_data`.`item_id` IN (
                SELECT `item`.`id` FROM `item` WHERE `item`.`resource_id` = ?
            )
        ', [$resource_id]);
    }

    protected function deleteItemLogs(int $resource_id): int
    {
        return DB::delete('
            DELETE FROM 
                `item_log`
            WHERE
                `item_log`.`item_id` IN (
                SELECT `item`.`id` FROM `item` WHERE `item`.`resource_id` = ?
            )
        ', [$resource_id]);
    }

    protected function deleteItems(int $resource_id): int
    {
        return DB::delete('
            DELETE FROM
                `item`
            WHERE `item`.`resource_id` = ?
        ', [$resource_id]);
    }

    protected function deleteItemSubcategories(int $resource_id): int
    {
        return DB::delete('
            DELETE FROM 
                `item_sub_category`
            WHERE
                `item_sub_category`.`item_category_id` IN (
                SELECT
                    `item_category`.`id`
                FROM
                    `item_category`
                WHERE `item_category`.`item_id` IN (
                    SELECT `item`.`id` FROM `item` WHERE `item`.`resource_id` = ?
               )
            )
        ', [$resource_id]);
    }

    protected function deleteItemTypeData(int $resource_type_id, int $resource_id): int
    {
        $item_type = Select::itemType($resource_type_id);

        return match ($item_type) {
            'allocated-expense' => $this->deleteAllocatedExpenseItems($resource_id),
            'budget' => $this->deleteBudgetItems($resource_id),
            'budget-pro' => $this->deleteBudgetProItems($resource_id),
            'game' => $this->deleteGameItems($resource_id),
            default => throw new \OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    protected function deletePartialTransfers(int $resource_id): int
    {
        return DB::delete('
            DELETE FROM
                `item_transfer`
            WHERE `item_transfer`.`item_id` IN (
                SELECT `item`.`id` FROM `item` WHERE `item`.`resource_id` = ?
            )
        ', [$resource_id]);
    }

    protected function deletePermittedUsers(int $resource_type_id): int
    {
        return DB::delete('
            DELETE FROM
                `permitted_user`
            WHERE
                `permitted_user`.`resource_type_id` = ?
        ', [$resource_type_id]);
    }

    protected function deleteResource(int $resource_id): int
    {
        return DB::delete('
            DELETE FROM
                `resource`
            WHERE `resource`.`id` = ?
        ', [$resource_id]);
    }

    protected function deleteResourceAndAllData(int $resource_type_id, int $resource_id)
    {
        $this->deletes = [];

        try {
            DB::transaction(function () use ($resource_type_id, $resource_id) {

                $this->deletes['data'] = $this->deleteItemData($resource_id);
                $this->deletes['logs'] = $this->deleteItemLogs($resource_id);
                $this->deletes['subcategories'] = $this->deleteItemSubcategories($resource_id);
                $this->deletes['categories'] = $this->deleteItemCategories($resource_id);
                $this->deletes['transfers'] = $this->deleteTransfers($resource_id);
                $this->deletes['partial-transfers'] = $this->deletePartialTransfers($resource_id);
                $this->deletes['item-type-data'] = $this->deleteItemTypeData($resource_type_id, $resource_id);
                $this->deletes['items'] = $this->deleteItems($resource_id);
                $this->deletes['resource-item-subtype'] = $this->deleteResourceItemSubType($resource_id);
                $this->deletes['resource'] = $this->deleteResource($resource_id);

            });


        } catch (Throwable $e) {
            $this->fail($e);
        }

        Notification::route('mail', Config::get('api.app.config.admin_email'))
            ->notify(new ResourceDeleted($this->deletes)
            );
    }

    protected function deleteResourceItemSubType(int $resource_id): int
    {
        return DB::delete('
            DELETE FROM
                `resource_item_subtype`
            WHERE `resource_item_subtype`.`resource_id` = ?
        ', [$resource_id]);
    }

    protected function deleteResourceType(int $resource_type_id): int
    {
        return DB::delete('
            DELETE FROM
                `resource_type`
            WHERE
                `resource_type`.`id` = ?
        ', [$resource_type_id]);
    }

    protected function deleteResourceTypeItemType(int $resource_type_id): int
    {
        return DB::delete('
            DELETE FROM
                `resource_type_item_type`
            WHERE
                `resource_type_item_type`.`resource_type_id` = ?
        ', [$resource_type_id]);
    }

    protected function deleteSubcategories(int $resource_type_id): int
    {
        return DB::delete('
            DELETE FROM
                `sub_category`
            WHERE
                `sub_category`.`category_id` IN (
                SELECT `category`.`id` FROM `category` WHERE `category`.`resource_type_id` = ?
            )
        ', [$resource_type_id]);
    }

    protected function deleteTransfers(int $resource_id): int
    {
        return DB::delete('
            DELETE FROM
                `item_partial_transfer`
            WHERE `item_partial_transfer`.`item_id` IN (
                SELECT `item`.`id` FROM `item` WHERE `item`.`resource_id` = ?
            )
        ', [$resource_id]);
    }
}
