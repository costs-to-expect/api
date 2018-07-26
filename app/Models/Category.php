<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Category model
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Category extends Model
{
    protected $table = 'category';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'category_id', 'id');
    }
}
