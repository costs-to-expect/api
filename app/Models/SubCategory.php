<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Sub category model
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SubCategory extends Model
{
    protected $table = 'sub_category';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function paginatedCollection(int $category_id, int $offset = 0, int $limit = 10)
    {
        return $this->where('category_id', '=', $category_id)
            ->get();
    }

    public function single(int $category_id, int $sub_category_id)
    {
        return $this->where('category_id', '=', $category_id)
            ->find($sub_category_id);
    }
}
