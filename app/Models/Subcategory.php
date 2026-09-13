<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

/**
 * @mixin QueryBuilder
 *
 * @property int $id
 * @property int $category_id
 * @property string $name
 * @property string $description
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2025
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Subcategory extends Model
{
    protected $table = 'sub_category';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function patchableFields(): array
    {
        return array_keys(Config::get('api.subcategory.validation-patch.fields'));
    }

    public function totalCount(
        int $resource_type_id,
        int $category_id,
        array $search_parameters = []
    ): int {
        $collection = $this->join('category', 'sub_category.category_id', 'category.id')
            ->where('sub_category.category_id', '=', $category_id)
            ->where('category.resource_type_id', '=', $resource_type_id);

        $collection = Utility::applySearchClauses($collection, $this->table, $search_parameters);

        return $collection->count();
    }

    public function paginatedCollection(
        int $resource_type_id,
        int $category_id,
        int $offset = 0,
        int $limit = 10,
        array $search_parameters = [],
        array $sort_parameters = []
    ): array {
        $last_updated_expression = $this->lastUpdatedExpression();
        
        $collection = $this
            ->select(
                'sub_category.id AS subcategory_id',
                'sub_category.name AS subcategory_name',
                'sub_category.description AS subcategory_description',
                'sub_category.created_at AS subcategory_created_at'
            )
            ->selectRaw($last_updated_expression->getValue(DB::connection()->getQueryGrammar()), [$category_id])
            ->join('category', 'sub_category.category_id', 'category.id')
            ->where('sub_category.category_id', '=', $category_id)
            ->where('category.resource_type_id', '=', $resource_type_id);

        $collection = Utility::applySearchClauses($collection, $this->table, $search_parameters);

        if (count($sort_parameters) > 0) {
            foreach ($sort_parameters as $field => $direction) {
                if ($field === 'created') {
                    $collection->orderBy($this->table . '.created_at', $direction);
                } else {
                    $collection->orderBy($this->table . '.' . $field, $direction);
                }
            }
        } else {
            $collection->orderBy($this->table . '.name', 'asc');
        }

        $collection->offset($offset)
            ->limit($limit);

        return $collection->get()->toArray();
    }

    public function single(
        int $category_id,
        int $subcategory_id
    ): ?array {
        $result = $this
            ->select(
                'sub_category.id AS subcategory_id',
                'sub_category.name AS subcategory_name',
                'sub_category.description AS subcategory_description',
                'sub_category.created_at AS subcategory_created_at'
            )
            ->where('category_id', '=', $category_id);

        $result = $result
            ->where($this->table . '.id', '=', $subcategory_id)
            ->get()
            ->toArray();

        if (count($result) === 0) {
            return null;
        }

        return $result[0];
    }

    public function instance(
        int $category_id,
        int $subcategory_id
    ): ?Subcategory {
        return $this->select(
            'sub_category.id',
            'sub_category.name',
            'sub_category.description'
        )->
            where('category_id', '=', $category_id)->
            find($subcategory_id);
    }

    public function instanceToArray(Subcategory $subcategory): array
    {
        return [
            'subcategory_id' => $subcategory->id,
            'subcategory_name' => $subcategory->name,
            'subcategory_description' => $subcategory->description,
            'subcategory_created_at' => $subcategory->created_at->toDateTimeString()
        ];
    }

    private function lastUpdatedExpression(): Expression
    {
        if (DB::getDriverName() === 'mysql') {
            return DB::raw('
                (
                    SELECT 
                        GREATEST(
                            MAX(sub_category.created_at), 
                            IFNULL(MAX(sub_category.updated_at), 0),
                            0
                        )
                    FROM 
                        sub_category
                    WHERE 
                        sub_category.category_id = ? 
                ) AS last_updated');
        }

        return DB::raw('(
                SELECT
                    MAX(
                        COALESCE(sub_category.created_at, 0),
                        COALESCE(sub_category.updated_at, 0),
                        0
                    )
                FROM
                    sub_category
                WHERE
                    sub_category.category_id = ?
            ) AS last_updated');
    }
}
