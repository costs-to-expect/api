<?php

namespace App\Jobs;

use App\ItemType\Select;
use App\Models\Permission;
use App\Models\PermittedUser;
use App\Notifications\FailedJob;
use App\Notifications\ResourceDeleted;
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

class DeleteResource implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected int $user_id;
    protected int $resource_type_id;
    protected int $resource_id;

    protected array $deletes;

    public int $tries = 1;

    public function __construct(
        int $user_id,
        int $resource_type_id,
        int $resource_id
    )
    {
        $this->user_id = $user_id;
        $this->resource_type_id = $resource_type_id;
        $this->resource_id = $resource_id;

        $this->deletes = [];
    }

    public function handle()
    {
        $permitted_users = (new Permission())->permittedUsersForResourceType($this->resource_type_id, $this->user_id);

        if (count($permitted_users) > 0) {
            $permitted_user = (new PermittedUser())->instance($this->resource_type_id, $this->user_id);
            $permitted_user?->delete();
        }

        if (count($permitted_users) === 0) {
            try {
                DB::transaction(function () {
                    $this->deletes['data'] = $this->deleteItemData($this->resource_id);
                    $this->deletes['logs'] = $this->deleteItemLogs($this->resource_id);
                    $this->deletes['subcategories'] = $this->deleteItemSubcategories($this->resource_id);
                    $this->deletes['categories'] = $this->deleteItemCategories($this->resource_id);
                    $this->deletes['transfers'] = $this->deleteTransfers($this->resource_id);
                    $this->deletes['partial-transfers'] = $this->deletePartialTransfers($this->resource_id);
                    $this->deletes['item-type-data'] = $this->deleteItemTypeData(
                        $this->resource_type_id,
                        $this->resource_id
                    );
                    $this->deletes['items'] = $this->deleteItems($this->resource_id);
                    $this->deletes['resource-item-subtype'] = $this->deleteResourceItemSubType($this->resource_id);
                    $this->deletes['resource'] = $this->deleteResource($this->resource_id);
                });
            } catch (Throwable $e) {
                throw new \Exception($e->getMessage());
            }

            Notification::route('mail', Config::get('api.app.config.admin_email'))
                ->notify(
                    new ResourceDeleted($this->deletes)
                );
        }
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

    protected function deleteResource(int $resource_id): int
    {
        return DB::delete('
            DELETE FROM
                `resource`
            WHERE `resource`.`id` = ?
        ', [$resource_id]);
    }

    protected function deleteResourceItemSubType(int $resource_id): int
    {
        return DB::delete('
            DELETE FROM
                `resource_item_subtype`
            WHERE `resource_item_subtype`.`resource_id` = ?
        ', [$resource_id]);
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
