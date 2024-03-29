<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

/**
 * @mixin QueryBuilder
 *
 * @property int $id
 * @property int $resource_type_id
 * @property string $name
 * @property string $description
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2023
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Category extends Model
{
    protected $table = 'category';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $fillable = ['name', 'description', 'resource_type_id'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(__CLASS__, 'category_id', 'id');
    }

    public function resourceType(): BelongsTo
    {
        return $this->belongsTo(ResourceType::class, 'resource_type_id', 'id');
    }

    public function patchableFields(): array
    {
        return array_keys(Config::get('api.category.validation-patch.fields'));
    }

    public function total(
        int $resource_type_id,
        array $viewable_resource_types,
        array $search_parameters = []
    ): int {
        $collection = $this
            ->select('category.id')
            ->join("resource_type", "category.resource_type_id", "resource_type.id")
            ->where('category.resource_type_id', '=', $resource_type_id);

        $collection = Utility::applyViewableResourceTypesClause(
            $collection,
            $viewable_resource_types
        );

        $collection = Utility::applySearchClauses($collection, $this->table, $search_parameters);

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
            ->selectRaw(
                '
                (
                    SELECT 
                        COUNT(`sub_category`.`id`) 
                    FROM 
                        `sub_category` 
                    WHERE 
                        `sub_category`.`category_id` = `category`.`id`
                ) AS `category_subcategories`'
            )
            ->selectRaw(
                '
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

        $collection = Utility::applyViewableResourceTypesClause(
            $collection,
            $viewable_resource_types
        );

        $collection = Utility::applySearchClauses($collection, $this->table, $search_parameters);

        if (count($sort_parameters) > 0) {
            foreach ($sort_parameters as $field => $direction) {
                switch ($field) {
                    case 'created':
                        $collection->orderBy($this->table . '.created_at', $direction);
                        break;

                    default:
                        $collection->orderBy($this->table . '.' . $field, $direction);
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
        }

        return $result->toArray();
    }

    public function instance(int $category_id): ?Category
    {
        return $this->find($category_id);
    }

    public function categoriesByResourceType(int $resource_type_id): array
    {
        return $this->join('resource_type', $this->table . '.resource_type_id', '=', 'resource_type.id')->
            where('resource_type.id', '=', $resource_type_id)->
            orderBy('category.name')->
            select(
                'category.id AS category_id',
                'category.name AS category_name',
                'category.description AS category_description'
            )->
            get()->
            toArray();
    }

    public function instanceToArray(Category $category): array
    {
        return [
            'category_id' => $category->id,
            'category_name' => $category->name,
            'category_description' => $category->description,
            'category_created_at' => $category->created_at->toDateTimeString(),
            'resource_type_id' => $category->resource_type_id,
            'resource_type_name' => $category->resourceType->name
        ];
    }
}
