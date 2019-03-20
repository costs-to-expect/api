<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Item category model
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemCategory extends Model
{
    protected $table = 'item_category';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function category()
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }

    public function item()
    {
        return $this->hasOne(Item::class, 'id', 'item_id');
    }

    public function paginatedCollection(
        int $resource_type_id,
        int $resource_id,
        int $item_id,
        int $offset = 0,
        int $limit = 10
    )
    {
        return $this->where('item_id', '=', $item_id)
            ->whereHas('item', function ($query) use ($resource_id, $resource_type_id) {
                $query->where('resource_id', '=', $resource_id)
                    ->whereHas('resource', function ($query) use ($resource_type_id) {
                        $query->where('resource_type_id', '=', $resource_type_id);
                    });
            })
            ->first();
    }

    public function single(
        int $resource_type_id,
        int $resource_id,
        int $item_id,
        int $item_category_id
    )
    {
        return $this->where('item_id', '=', $item_id)
            ->whereHas('item', function ($query) use ($resource_id, $resource_type_id) {
                $query->where('resource_id', '=', $resource_id)
                    ->whereHas('resource', function ($query) use ($resource_type_id) {
                        $query->where('resource_type_id', '=', $resource_type_id);
                    });
            })
            ->find($item_category_id);
    }
}
