<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Item model
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemSubCategory extends Model
{
    protected $table = 'item_sub_category';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function sub_category()
    {
        return $this->hasOne(SubCategory::class, 'id', 'sub_category_id');
    }

    public function item()
    {
        return $this->hasOne(Item::class, 'id', 'item_id');
    }
}
