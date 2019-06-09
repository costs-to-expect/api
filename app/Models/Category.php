<?php
declare(strict_types=1);

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

/**
 * Category model
 *
 * Single() exists in this model to be consistent with all the other models, it
 * is simply a synonym for find().
 *
 * Categories are private if they are related to a private resource type
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
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

    /**
     * Return the paginated collection
     *
     * @param boolean $include_private Should we include private categories?
     * @param array $collection_parameters
     * @param integer $offset
     * @param integer $limit
     *
     * @return array
     */
    public function paginatedCollection(
        bool $include_private,
        array $collection_parameters,
        int $offset = 0,
        int $limit = 10
    ): array {
        $collection = $this->select(
            'category.id AS category_id',
            'category.name AS category_name',
            'category.description AS category_description',
            'category.created_at AS category_created_at',
            'category.updated_at AS category_updated_at',
            'resource_type.id AS resource_type_id',
            'resource_type.name AS resource_type_name',
            'resource_type.name AS resource_type_name'
        )->selectRaw('
            (
                SELECT 
                    COUNT(`sub_category`.`id`) 
                FROM 
                    `sub_category` 
                WHERE 
                    `sub_category`.`category_id` = `category`.`id`
            ) AS `category_sub_categories`'
        )->join("resource_type", "category.resource_type_id", "resource_type.id");

        if (
            array_key_exists('resource_type', $collection_parameters) === true &&
            $collection_parameters['resource_type'] !== null
        ) {
            $collection->where('category.resource_type_id', '=', $collection_parameters['resource_type']);
        }

        if ($include_private === false) {
            $collection->where('resource_type.private', '=', 0);
        }

        return $collection->get()->toArray();
    }

    /**
     * Return a single item
     *
     * @param integer $category_id
     *
     * @return array
     */
    public function single(int $category_id): array
    {
        return $this->join('resource_type', $this->table . '.resource_type_id', '=', 'resource_type.id')
            ->where('category.id', '=', intval($category_id))
            ->orderBy('category.name')
            ->select(
                'category.id AS category_id',
                'category.name AS category_name',
                'category.description AS category_description',
                'category.created_at AS category_created_at',
                'category.updated_at AS category_updated_at',
                DB::raw('(SELECT COUNT(sub_category.id) FROM sub_category WHERE sub_category.category_id = category.id) AS category_sub_categories'),
                'resource_type.id AS resource_type_id',
                'resource_type.name AS resource_type_name'
            )
            ->first()
            ->toArray();
    }

    /**
     * Fetch all the categories assigned to the resource type
     *
     * @param integer $resource_type_id
     *
     * @return \Illuminate\Support\Collection
     */
    public function categoriesByResourceType(int $resource_type_id)
    {
        return $this->join('resource_type', $this->table . '.resource_type_id', '=', 'resource_type.id')
            ->where('resource_type.id', '=', intval($resource_type_id))
            ->orderBy('category.name')
            ->select(
                'category.id AS category_id',
                'category.name AS category_name',
                'category.description AS category_description'
            )
            ->get();
    }
}
