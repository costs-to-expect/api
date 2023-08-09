<?php

declare(strict_types=1);

namespace App\Models;

use App\HttpRequest\Validate\Boolean;
use App\ItemType\Select;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

/**
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2023
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Utility
{
    public static function applySearchClauses(
        $collection,
        string $table,
        array $search_parameters = []
    ) {
        if (count($search_parameters) > 0) {
            foreach ($search_parameters as $field => $search_term) {
                $collection->where($table . '.' . $field, 'LIKE', '%' . $search_term . '%');
            }
        }

        return $collection;
    }

    public static function applyFilteringClauses(
        $collection,
        string $table,
        array $filter_parameters = []
    ) {
        if (count($filter_parameters) > 0) {
            foreach ($filter_parameters as $field => $range) {
                $collection->where($table . '.' . $field, '>=', $range['from']);
                $collection->where($table . '.' . $field, '<=', $range['to']);
            }
        }

        return $collection;
    }

    public static function applyExcludeFutureUnpublishedClause(
        $collection,
        array $parameters
    ) {
        if (
            array_key_exists('include-unpublished', $parameters) === false ||
            Boolean::convertedValue($parameters['include-unpublished']) === false
        ) {
            $collection->where(static function ($collection) {
                $collection
                    ->whereNull('item_type_allocated_expense.publish_after')
                    ->orWhereRaw('item_type_allocated_expense.publish_after < NOW()');
            });
        }

        return $collection;
    }

    public static function applyViewableResourceTypesClause(
        $collection,
        array $viewable_resource_types
    ) {
        return $collection->whereIn('resource_type.id', $viewable_resource_types);
    }

    public static function deleteCategories(int $resource_type_id): int
    {
        return DB::delete('
            DELETE FROM
                `category`
            WHERE
                `category`.`resource_type_id` = ?
        ', [$resource_type_id]);
    }

    public static function deleteItemCategories(int $resource_id): int
    {
        return DB::delete('
            DELETE FROM 
                `item_category`
            WHERE `item_category`.`item_id` IN (
                SELECT `item`.`id` FROM `item` WHERE `item`.`resource_id` = ?
            )
        ', [$resource_id]);
    }

    public static function deleteItemData(int $resource_id): int
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

    public static function deleteItemLogs(int $resource_id): int
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

    public static function deleteItems(int $resource_id): int
    {
        return DB::delete('
            DELETE FROM
                `item`
            WHERE `item`.`resource_id` = ?
        ', [$resource_id]);
    }

    public static function deleteItemSubcategories(int $resource_id): int
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

    public static function deleteItemTypeData(int $resource_type_id, int $resource_id): int
    {
        $item_type = Select::itemType($resource_type_id);

        return match ($item_type) {
            'allocated-expense' => self::deleteItemTypeDataAllocatedExpense($resource_id),
            'budget' => self::deleteItemTypeDataBudget($resource_id),
            'budget-pro' => self::deleteItemTypeDataBudgetPro($resource_id),
            'game' => self::deleteItemTypeDataGame($resource_id),
            default => throw new \OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    private static function deleteItemTypeDataAllocatedExpense(int $resource_id): int
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

    private static function deleteItemTypeDataBudget(int $resource_id): int
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

    private static function deleteItemTypeDataBudgetPro(int $resource_id): int
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

    private static function deleteItemTypeDataGame(int $resource_id): int
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

    public static function deletePartialTransfers(int $resource_id): int
    {
        return DB::delete('
            DELETE FROM
                `item_transfer`
            WHERE `item_transfer`.`item_id` IN (
                SELECT `item`.`id` FROM `item` WHERE `item`.`resource_id` = ?
            )
        ', [$resource_id]);
    }

    public static function deletePermittedUsers(int $resource_type_id): int
    {
        return DB::delete('
            DELETE FROM
                `permitted_user`
            WHERE
                `permitted_user`.`resource_type_id` = ?
        ', [$resource_type_id]);
    }

    public static function deleteResource(int $resource_id): int
    {
        return DB::delete('
            DELETE FROM
                `resource`
            WHERE `resource`.`id` = ?
        ', [$resource_id]);
    }

    public static function deleteResourceItemSubType(int $resource_id): int
    {
        return DB::delete('
            DELETE FROM
                `resource_item_subtype`
            WHERE `resource_item_subtype`.`resource_id` = ?
        ', [$resource_id]);
    }

    public static function deleteResourceType(int $resource_type_id): int
    {
        return DB::delete('
            DELETE FROM
                `resource_type`
            WHERE
                `resource_type`.`id` = ?
        ', [$resource_type_id]);
    }

    public static function deleteResourceTypeItemType(int $resource_type_id): int
    {
        return DB::delete('
            DELETE FROM
                `resource_type_item_type`
            WHERE
                `resource_type_item_type`.`resource_type_id` = ?
        ', [$resource_type_id]);
    }

    public static function deleteSubcategories(int $resource_type_id): int
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

    public static function deleteTransfers(int $resource_id): int
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
