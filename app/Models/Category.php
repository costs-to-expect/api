<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

/**
 * Category model
 *
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2021
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Category extends Model
{
    protected $table = 'category';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $fillable = ['name', 'description', 'resource_type_id'];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    /**
     * Return an array of the fields that can be PATCHed.
     *
     * @return array
     */
    public function patchableFields(): array
    {
        return array_keys(Config::get('api.category.validation.PATCH.fields'));
    }

    public function total(
        int $resource_type_id,
        array $viewable_resource_types,
        array $search_parameters = []
    ): int
    {
        $collection = $this
            ->select('category.id')
            ->join("resource_type", "category.resource_type_id", "resource_type.id")
            ->where('category.resource_type_id', '=', $resource_type_id);

        $collection = Clause::applyViewableResourceTypes(
            $collection,
            $viewable_resource_types
        );

        $collection = Clause::applySearch($collection, $this->table, $search_parameters);

        return $collection->count();
    }

    public function paginatedCollection(
        int $resource_type_id,
        array $viewable_resource_types,
        int $offset = 0,
        int $limit = 10,
        array $search_parameters = [],
        array $sort_parameters = []
    ): array {
        $collection = $this
            ->select(
                'category.id AS category_id',
                'category.name AS category_name',
                'category.description AS category_description',
                'category.created_at AS category_created_at',
                'category.updated_at AS category_updated_at',
                'resource_type.id AS resource_type_id',
                'resource_type.name AS resource_type_name',
                'resource_type.name AS resource_type_name'
            )
            ->selectRaw('
                (
                    SELECT 
                        COUNT(`sub_category`.`id`) 
                    FROM 
                        `sub_category` 
                    WHERE 
                        `sub_category`.`category_id` = `category`.`id`
                ) AS `category_subcategories`'
            )
            ->selectRaw('
                (
                    SELECT 
                        GREATEST(
                            MAX(category.created_at), 
                            IFNULL(MAX(category.updated_at), 0),
                            0
                        )
                    FROM 
                        category
                    WHERE 
                        category.resource_type_id = ? 
                ) AS last_updated',
                [
                    $resource_type_id
                ]
            )
            ->join("resource_type", "category.resource_type_id", "resource_type.id")
            ->where('category.resource_type_id', '=', $resource_type_id);

        $collection = Clause::applyViewableResourceTypes(
            $collection,
            $viewable_resource_types
        );

        $collection = Clause::applySearch($collection, $this->table, $search_parameters);

        if (count($sort_parameters) > 0) {
            foreach ($sort_parameters as $field => $direction) {
                switch ($field) {
                    case 'created':
                        $collection->orderBy('category.created_at', $direction);
                        break;

                    default:
                        $collection->orderBy('category.' . $field, $direction);
                        break;
                }
            }
        } else {
            $collection->orderBy('category.name', 'asc');
        }

        $collection->offset($offset);
        $collection->limit($limit);

        return $collection->get()->toArray();
    }

    /**
     * Return a single item
     *
     * @param integer $resource_type_id
     * @param integer $category_id
     *
     * @return array|null
     */
    public function single(int $resource_type_id, int $category_id): ?array
    {
        $result = $this->join('resource_type', $this->table . '.resource_type_id', '=', 'resource_type.id')->
            where('category.id', '=', $category_id)->
            where('category.resource_type_id', '=', $resource_type_id)->
            orderBy('category.name')->
            select(
                'category.id AS category_id',
                'category.name AS category_name',
                'category.description AS category_description',
                'category.created_at AS category_created_at',
                'category.updated_at AS category_updated_at',
                DB::raw('(SELECT COUNT(sub_category.id) FROM sub_category WHERE sub_category.category_id = category.id) AS category_subcategories'),
                'resource_type.id AS resource_type_id',
                'resource_type.name AS resource_type_name'
            )->
            first();

        if ($result === null) {
            return null;
        } else {
            return $result->toArray();
        }
    }

    /**
     * Return an instance of a Category
     *
     * @param integer $category_id
     *
     * @return Category|null
     */
    public function instance(int $category_id): ?Category
    {
        return $this->find($category_id);
    }

    /**
     * Fetch all the categories assigned to the resource type
     *
     * @param integer $resource_type_id
     *
     * @return array
     */
    public function categoriesByResourceType(int $resource_type_id): array
    {
        return $this->join('resource_type', $this->table . '.resource_type_id', '=', 'resource_type.id')->
            where('resource_type.id', '=', intval($resource_type_id))->
            orderBy('category.name')->
            select(
                'category.id AS category_id',
                'category.name AS category_name',
                'category.description AS category_description'
            )->
            get()->
            toArray();
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
