<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Category model
 *
 * Single() exists in this model to be consistent with all the other models, it
 * is simply a synonym for find().
 *
 * Categories are private if they are related to a private resource type
 *
 * @mixin QueryBuilder
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
     * @param boolean $include_private
     * @param array $parameters
     * @param array $search_parameters
     *
     * @return integer
     */
    public function totalCount(
        bool $include_private,
        array $parameters = [],
        array $search_parameters = []
    ): int
    {
        $collection = $this->select('category.id')->
            join("resource_type", "category.resource_type_id", "resource_type.id");

        if (
            array_key_exists('resource_type', $parameters) === true &&
            $parameters['resource_type'] !== null
        ) {
            $collection->where('category.resource_type_id', '=', $parameters['resource_type']);
        }

        if ($include_private === false) {
            $collection->where('resource_type.private', '=', 0);
        }

        if (count($search_parameters) > 0) {
            foreach ($search_parameters as $field => $search_term) {
                $collection->where('category.' . $field, 'LIKE', '%' . $search_term . '%');
            }
        }

        return count($collection->get());
    }

    /**
     * Return the paginated collection
     *
     * @param boolean $include_private Should we include private categories?
     * @param integer $offset
     * @param integer $limit
     * @param array $parameters
     * @param array $search_parameters
     *
     * @return array
     */
    public function paginatedCollection(
        bool $include_private,
        int $offset = 0,
        int $limit = 10,
        array $parameters = [],
        array $search_parameters = []
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
            ) AS `category_subcategories`'
        )->join("resource_type", "category.resource_type_id", "resource_type.id");

        if (
            array_key_exists('resource_type', $parameters) === true &&
            $parameters['resource_type'] !== null
        ) {
            $collection->where('category.resource_type_id', '=', $parameters['resource_type']);
        }

        if ($include_private === false) {
            $collection->where('resource_type.private', '=', 0);
        }

        if (count($search_parameters) > 0) {
            foreach ($search_parameters as $field => $search_term) {
                $collection->where('category.' . $field, 'LIKE', '%' . $search_term . '%');
            }
        }

        $collection->offset($offset);
        $collection->limit($limit);

        return $collection->get()->toArray();
    }

    /**
     * Return a single item
     *
     * @param integer $category_id
     *
     * @return array|null
     */
    public function single(int $category_id): ?array
    {
        $result = $this->join('resource_type', $this->table . '.resource_type_id', '=', 'resource_type.id')
            ->where('category.id', '=', intval($category_id))
            ->orderBy('category.name')
            ->select(
                'category.id AS category_id',
                'category.name AS category_name',
                'category.description AS category_description',
                'category.created_at AS category_created_at',
                'category.updated_at AS category_updated_at',
                DB::raw('(SELECT COUNT(sub_category.id) FROM sub_category WHERE sub_category.category_id = category.id) AS category_subcategories'),
                'resource_type.id AS resource_type_id',
                'resource_type.name AS resource_type_name'
            )
            ->first();

        if ($result === null) {
            return null;
        } else {
            return $result->toArray();
        }
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

    /**
     * Convert the model instance to an array for use with the transformer
     *
     * @param Category $category
     *
     * @return array
     */
    public function instanceToArray(Category $category): array
    {
        return [
            'category_id' => $category->id,
            'category_name' => $category->name,
            'category_description' => $category->description,
            'category_created_at' => $category->created_at->toDateTimeString(),
            'resource_type_id' => $category->resource_type_id
        ];
    }
}
