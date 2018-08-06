<?php

namespace App\Models;

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

    public function subCategories()
    {
        return $this->hasMany(SubCategory::class, 'category_id', 'id');
    }

    public function numberOfSubCategories()
    {
        return $this->hasMany(SubCategory::class, 'category_id', 'id')->count();
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function paginatedCollection(int $offset = 0, int $limit = 10)
    {
        return $this->all();
    }

    public function single(int $category_id)
    {
        return $this->find($category_id);
    }
}
