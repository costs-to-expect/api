<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Sub category model
 *
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
     * @param integer $offset
     * @param integer $limit
     *
     * @return array
     */
    public function paginatedCollection(
        int $category_id,
        int $offset = 0,
        int $limit = 10
    ): array
    {
        return $this->where('category_id', '=', $category_id)->
            orderBy("name")->
            get()->
            toArray();
    }

    public function single(
        int $category_id,
        int $sub_category_id
    ): array
    {
        return $this->where('category_id', '=', $category_id)->
            find($sub_category_id)->
            toArray();
    }

    public function subCategorySummary(int $resource_type_id, int $resource_id)
    {
        $query = DB::raw("
                SELECT 
                    category.name AS category, 
                    sub_category.name AS sub_category,
                    IFNULL(assigned_items.actualised_total, 0) AS actualised_total,
                    IFNULL(assigned_items.items, 0) AS items
                FROM 
                    sub_category
                INNER JOIN 
                    category ON 
                        sub_category.category_id = category.id
                INNER JOIN 
                    resource_type ON 
                        category.resource_type_id = resource_type.id
                INNER JOIN 
                    resource ON 
                        resource_type.id = resource.resource_type_id
                LEFT JOIN (
                    SELECT 
                        item_category.category_id,
                        item_sub_category.sub_category_id,
                        SUM(item.actualised_total) AS actualised_total,
                        COUNT(item.id) AS items
                    FROM 
                        item
                    INNER JOIN 
                        item_category ON 
                            item.id = item_category.item_id
                    INNER JOIN 
                        item_sub_category ON 
                            item_category.id = item_sub_category.item_category_id
                    GROUP BY 
                        item_sub_category.sub_category_id,
                        item_category.category_id
                ) AS assigned_items ON 
                    assigned_items.sub_category_id = sub_category.id        
                WHERE 
                    resource_type.id = :resource_type_id
                    AND 
                    resource.id = :resource_id
                ORDER BY 
                    category.name ASC, 
                    sub_category.name ASC")->getValue();

        return DB::select(
            $query,
            [
                'resource_type_id' => $resource_type_id,
                'resource_id' => $resource_id
            ]
        );
    }
}
