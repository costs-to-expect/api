<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

/**
 * Category model
 *
 * Single() exists in this model to be consistent with all the other models, it is
 * simply a synonym for find()
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Category extends Model
{
    protected $table = 'category';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function paginatedCollection(int $offset = 0, int $limit = 10)
    {
        return $this->join('resource_type', $this->table . '.resource_type_id', '=', 'resource_type.id')
            ->orderBy('category.name')
            ->select(
                'category.id AS category_id',
                'category.name AS category_name',
                'category.id AS category_id',
                'category.description AS category_description',
                'category.created_at AS category_created_at',
                'category.updated_at AS category_updated_at',
                DB::raw('(SELECT COUNT(sub_category.id) FROM sub_category WHERE sub_category.category_id = category.id) AS category_sub_categories')
            )
            ->get();
    }

    public function single(int $category_id)
    {
        return $this->join('resource_type', $this->table . '.resource_type_id', '=', 'resource_type.id')
            ->orderBy('category.name')
            ->select(
                'category.id AS category_id',
                'category.name AS category_name',
                'category.id AS category_id',
                'category.description AS category_description',
                'category.created_at AS category_created_at',
                'category.updated_at AS category_updated_at',
                DB::raw('(SELECT COUNT(sub_category.id) FROM sub_category WHERE sub_category.category_id = category.id) AS category_sub_categories')
            )
            ->first();
    }
}
