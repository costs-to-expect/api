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
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemType extends Model
{
    protected $table = 'item_type';

    protected $guarded = ['id', 'name', 'description', 'created_at', 'updated_at'];

    /**
     * Fetch the item types, id, name and description only
     *
     * @return array
     */
    public function minimisedCollection(): array
    {
        return $this->select(
                'item_type.id AS item_type_id',
                'item_type.name AS item_type_name',
                'item_type.description AS item_type_description'
            )->
            get()->
            toArray();
    }
}
