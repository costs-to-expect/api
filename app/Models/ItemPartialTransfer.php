<?php

declare(strict_types=1);

namespace App\Models;

use App\ItemType\Select;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin QueryBuilder
 *
 * @property int $id
 * @property int $resource_type_id
 * @property int $from
 * @property int $to
 * @property int $item_id
 * @property int $percentage
 * @property int $transferred_by
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2025
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemPartialTransfer extends Model
{
    protected $table = 'item_partial_transfer';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Partial transfers are only supported by item types that store a
     * name/description on their own item-type table; the join table is
     * resolved explicitly here rather than assumed, so it works for any
     * of them
     */
    private function itemTypeTable(int $resource_type_id): string
    {
        $item_type = Select::itemType($resource_type_id);

        return match ($item_type) {
            'allocated-expense' => 'item_type_allocated_expense',
            'allocated-transaction' => 'item_type_allocated_transaction',
            default => throw new \OutOfRangeException('No item type definition for ' . $item_type, 500),
        };
    }

    public function paginatedCollection(
        int $resource_type_id,
        array $viewable_resource_types,
        int $offset = 0,
        int $limit = 10,
        array $parameters = []
    ): array {
        $item_type_table = $this->itemTypeTable($resource_type_id);

        $collection = $this
            ->select(
                $this->table . '.id',
                $this->table . '.resource_type_id',
                $this->table . '.percentage',
                $this->table . '.item_id AS item_item_id',
                $this->table . '.created_at',
                $item_type_table . '.name AS item_name',
                $item_type_table . '.description AS item_description',
                'from_resource.id AS from_resource_id',
                'from_resource.name AS from_resource_name',
                'to_resource.id AS to_resource_id',
                'to_resource.name AS to_resource_name',
                'users.id AS user_id',
                'users.name AS user_name'
            )
            ->join("resource_type", $this->table . ".resource_type_id", "resource_type.id")
            ->join("resource AS from_resource", $this->table . ".from", "from_resource.id")
            ->join("resource AS to_resource", $this->table . ".to", "to_resource.id")
            ->join("item", $this->table . ".item_id", "item.id")
            ->join($item_type_table, "item.id", $item_type_table . ".item_id")
            ->join("users", $this->table . ".transferred_by", "users.id")
            ->where($this->table . '.resource_type_id', '=', $resource_type_id);

        if (array_key_exists('item', $parameters) === true &&
            $parameters['item'] !== null) {
            $collection->where($this->table . '.item_id', '=', $parameters['item']);
        }

        $collection = Utility::applyViewableResourceTypesClause(
            $collection,
            $viewable_resource_types
        );

        $collection->offset($offset);
        $collection->limit($limit);

        return $collection->get()->toArray();
    }

    public function single(
        int $resource_type_id,
        int $item_partial_transfer_id
    ): ?array {
        $item_type_table = $this->itemTypeTable($resource_type_id);

        $result = $this
            ->join("resource_type", $this->table . ".resource_type_id", "resource_type.id")
            ->join("resource AS from_resource", $this->table . ".from", "from_resource.id")
            ->join("resource AS to_resource", $this->table . ".to", "to_resource.id")
            ->join("item", $this->table . ".item_id", "item.id")
            ->join($item_type_table, "item.id", $item_type_table . ".item_id")
            ->join("users", $this->table . ".transferred_by", "users.id")
            ->where($this->table . '.resource_type_id', '=', $resource_type_id)
            ->where($this->table . '.id', '=', $item_partial_transfer_id)
            ->select(
                $this->table . '.id',
                $this->table . '.resource_type_id',
                $this->table . '.percentage',
                $this->table . '.item_id AS item_item_id',
                $item_type_table . '.name AS item_name',
                $item_type_table . '.description AS item_description',
                $this->table . '.created_at',
                'from_resource.id AS from_resource_id',
                'from_resource.name AS from_resource_name',
                'to_resource.id AS to_resource_id',
                'to_resource.name AS to_resource_name',
                'users.id AS user_id',
                'users.name AS user_name'
            )
            ->first();

        if ($result === null) {
            return null;
        }

        return $result->toArray();
    }

    public function total(
        int $resource_type_id,
        array $viewable_resource_types,
        array $parameters = []
    ): int {
        $collection = $this
            ->select($this->table . '.id')
            ->join("resource_type", $this->table . ".resource_type_id", "resource_type.id")
            ->join("resource AS from_resource", $this->table . ".from", "from_resource.id")
            ->join("resource AS to_resource", $this->table . ".to", "to_resource.id")
            ->join("item", $this->table . ".item_id", "item.id")
            ->join("users", $this->table . ".transferred_by", "users.id")
            ->where($this->table . '.resource_type_id', '=', $resource_type_id);

        if (array_key_exists('item', $parameters) === true &&
            $parameters['item'] !== null) {
            $collection->where($this->table . '.item_id', '=', $parameters['item']);
        }

        $collection = Utility::applyViewableResourceTypesClause(
            $collection,
            $viewable_resource_types,
        );

        return $collection->count();
    }

    public function deleteTransfers(int $item_id): ?int
    {
        return $this->where($this->table . '.item_id', '=', $item_id)->delete();
    }
}
