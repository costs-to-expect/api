<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Sub category model
 *
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
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

    /**
     * @param integer $category_id
     * @param array $search_parameters
     *
     * @return integer
     */
    public function totalCount(
        int $category_id,
        array $search_parameters = []
    ): int
    {
        $collection = $this->where('category_id', '=', $category_id);

        if (count($search_parameters) > 0) {
            foreach ($search_parameters as $field => $search_term) {
                $collection->where('sub_category.' . $field, 'LIKE', '%' . $search_term . '%');
            }
        }

        return count($collection->get());
    }

    /**
     * @param integer $category_id
     * @param integer $offset
     * @param integer $limit
     * @param array $search_parameters
     *
     * @return array
     */
    public function paginatedCollection(
        int $category_id,
        int $offset = 0,
        int $limit = 10,
        array $search_parameters = []
    ): array
    {
        $collection = $this->select(
                'sub_category.id AS subcategory_id',
                'sub_category.name AS subcategory_name',
                'sub_category.description AS subcategory_description',
                'sub_category.created_at AS subcategory_created_at'
            )->
            where('category_id', '=', $category_id);

        if (count($search_parameters) > 0) {
            foreach ($search_parameters as $field => $search_term) {
                $collection->where('sub_category.' . $field, 'LIKE', '%' . $search_term . '%');
            }
        }

        $collection->orderBy("name")->
            offset($offset)->
            limit($limit);

        return $collection->get()->
            toArray();
    }

    public function single(
        int $category_id,
        int $sub_category_id
    ): ?array
    {
        $result = $this->select(
                'sub_category.id AS subcategory_id',
                'sub_category.name AS subcategory_name',
                'sub_category.description AS subcategory_description',
                'sub_category.created_at AS subcategory_created_at'
            )->
            where('category_id', '=', $category_id)->
            find($sub_category_id);

        if ($result !== null) {
            return $result->toArray();
        } else {
            return null;
        }
    }

    /**
     * Convert the model instance to an array for use with the transformer
     *
     * @param SubCategory $subcategory
     *
     * @return array
     */
    public function instanceToArray(SubCategory $subcategory): array
    {
        return [
            'subcategory_id' => $subcategory->id,
            'subcategory_name' => $subcategory->name,
            'subcategory_description' => $subcategory->description,
            'subcategory_created_at' => $subcategory->created_at->toDateTimeString()
        ];
    }
}
