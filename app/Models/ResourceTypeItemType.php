<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;

/**
 * Resource type item type model
 *
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceTypeItemType extends Model
{
    protected $table = 'resource_type_item_type';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Fetch the item type assigned to the requested resource type
     *
     * @param integer $resource_type_id
     *
     * @return string|null
     */
    public function itemType(int $resource_type_id): ?string
    {
        $collection = $this->join(
            'item_type',
            'resource_type_item_type.item_type_id',
            'item_type.id')->
            where('resource_type_item_type.resource_type_id', '=', $resource_type_id)->
            first(['item_type.name']);

        if ($collection !== null) {
            return $collection->toArray()['name'];
        } else {
            return null;
        }
    }

    /**
     * Return an instance of the resource type item type
     *
     * @param integer $resource_type_id
     *
     * @return ResourceAccess|null
     */
    public function instance(int $resource_type_id): ?ResourceTypeItemType
    {
        return $this->where('resource_type_id', '=', $resource_type_id)->
            first();
    }
}
