<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;

/**
 * Item type model
 *
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemPartialTransfer extends Model
{
    protected $table = 'item_partial_transfer';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Return the paginated collection
     *
     * @param integer $resource_type_id
     * @param array $permitted_resource_types
     * @param boolean $include_public
     * @param integer $offset
     * @param integer $limit
     * @param array $parameters
     *
     * @return array
     */
    public function paginatedCollection(
        int $resource_type_id,
        array $permitted_resource_types,
        bool $include_public,
        int $offset = 0,
        int $limit = 10,
        array $parameters = []
    ): array {
        $collection = $this->select(
                $this->table . '.id',
                $this->table . '.percentage',
                $this->table . '.item_id',
                $this->table . '.created_at',
                'from_resource.id AS from_resource_id',
                'from_resource.name AS from_resource_name',
                'to_resource.id AS to_resource_id',
                'to_resource.name AS to_resource_name',
                'users.id AS user_id',
                'users.name AS user_name'
            )->
            join("resource_type",$this->table . ".resource_type_id","resource_type.id")->
            join("resource AS from_resource",$this->table . ".from","from_resource.id")->
            join("resource AS to_resource",$this->table . ".to","to_resource.id")->
            join("item",$this->table . ".item_id","item.id")->
            join("users",$this->table . ".transferred_by","users.id")->
            where($this->table .'.resource_type_id', '=', $resource_type_id);

        if (array_key_exists('item', $parameters) === true &&
            $parameters['item'] !== null) {
            $collection->where($this->table .'.item_id', '=', $parameters['item']);
        }

        $collection = Clause::applyResourceTypeCollectionCondition(
            $collection,
            $permitted_resource_types,
            $include_public
        );

        $collection->offset($offset);
        $collection->limit($limit);

        return $collection->get()->toArray();
    }

    /**
     * Return a single partial transfer
     *
     * @param integer $resource_type_id
     * @param integer $item_partial_transfer_id
     *
     * @return array|null
     */
    public function single(
        int $resource_type_id,
        int $item_partial_transfer_id
    ): ?array
    {
        $result = $this->join("resource_type",$this->table . ".resource_type_id","resource_type.id")->
        join("resource AS from_resource",$this->table . ".from","from_resource.id")->
        join("resource AS to_resource",$this->table . ".to","to_resource.id")->
        join("item",$this->table . ".item_id","item.id")->
        join("users",$this->table . ".transferred_by","users.id")->
        where($this->table .'.resource_type_id', '=', $resource_type_id)->
        where($this->table .'.id', '=', $item_partial_transfer_id)->
        select(
            $this->table . '.id',
            $this->table . '.percentage',
            $this->table . '.item_id',
            $this->table . '.created_at',
            'from_resource.id AS from_resource_id',
            'from_resource.name AS from_resource_name',
            'to_resource.id AS to_resource_id',
            'to_resource.name AS to_resource_name',
            'users.id AS user_id',
            'users.name AS user_name'
        )->
        first();

        if ($result === null) {
            return null;
        } else {
            return $result->toArray();
        }
    }

    /**
     * @param integer $resource_type_id
     * @param array $permitted_resource_types
     * @param boolean $include_public
     * @param array $parameters
     *
     * @return integer
     */
    public function total(
        int $resource_type_id,
        array $permitted_resource_types,
        bool $include_public,
        array $parameters = []
    ): int
    {
        $collection = $this->select($this->table . '.id')->
            join("resource_type",$this->table . ".resource_type_id","resource_type.id")->
            join("resource AS from_resource",$this->table . ".from","from_resource.id")->
            join("resource AS to_resource",$this->table . ".to","to_resource.id")->
            join("item",$this->table . ".item_id","item.id")->
            join("users",$this->table . ".transferred_by","users.id")->
            where($this->table .'.resource_type_id', '=', $resource_type_id);

        if (array_key_exists('item', $parameters) === true &&
            $parameters['item'] !== null) {
            $collection->where($this->table .'.item_id', '=', $parameters['item']);
        }

        $collection = Clause::applyResourceTypeCollectionCondition(
            $collection,
            $permitted_resource_types,
            $include_public
        );

        return $collection->count();
    }
}
