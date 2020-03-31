<?php
declare(strict_types=1);

namespace App\Models;

use App\Utilities\Model as ModelUtility;
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
     * @param boolean $include_public Should we include categories assigned to public resources
     * @param integer $offset
     * @param integer $limit
     *
     * @return array
     */
    public function paginatedCollection(
        int $resource_type_id,
        array $permitted_resource_types,
        bool $include_public,
        int $offset = 0,
        int $limit = 10
    ): array {
        $collection = $this->select(
            $this->table . '.id',
            $this->table . '.percentage',
            $this->table . '.created_at'
        )->
        join("resource_type",$this->table . ".resource_type_id","resource_type.id")->
        join("resource AS from_resource",$this->table . ".from","from_resource.id")->
        join("resource AS to_resource",$this->table . ".to","to_resource.id")->
        join("item",$this->table . ".item_id","item.id")->
        join("users",$this->table . ".transferred_by","users.id")->
        where($this->table .'.resource_type_id', '=', $resource_type_id);

        $collection = ModelUtility::applyResourceTypeCollectionCondition(
            $collection,
            $permitted_resource_types,
            $include_public
        );

        $collection->offset($offset);
        $collection->limit($limit);

        return $collection->get()->toArray();
    }

    /**
     * @param integer $resource_type_id
     * @param array $permitted_resource_types
     * @param boolean $include_public
     *
     * @return integer
     */
    public function total(
        int $resource_type_id,
        array $permitted_resource_types,
        bool $include_public
    ): int
    {
        $collection = $this->select($this->table . '.id')->
            join("resource_type",$this->table . ".resource_type_id","resource_type.id")->
            join("resource AS from_resource",$this->table . ".from","from_resource.id")->
            join("resource AS to_resource",$this->table . ".to","to_resource.id")->
            join("item",$this->table . ".item_id","item.id")->
            join("users",$this->table . ".transferred_by","users.id")->
            where($this->table .'.resource_type_id', '=', $resource_type_id);

        $collection = ModelUtility::applyResourceTypeCollectionCondition(
            $collection,
            $permitted_resource_types,
            $include_public
        );

        return $collection->count();
    }
}
