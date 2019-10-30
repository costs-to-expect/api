<?php
declare(strict_types=1);

namespace App\Models;

use App\Utilities\Model as ModelUtility;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

/**
 * Sub category model
 *
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Subcategory extends Model
{
    protected $table = 'sub_category';

    protected $guarded = ['id', 'created_at', 'updated_at'];

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
        return array_keys(Config::get('api.subcategory.validation.PATCH.fields'));
    }

    /**
     * @param integer $resource_type_id
     * @param integer $category_id
     * @param array $search_parameters
     *
     * @return integer
     */
    public function totalCount(
        int $resource_type_id,
        int $category_id,
        array $search_parameters = []
    ): int
    {
        $collection = $this->join('category', 'sub_category.category_id', 'category.id')->
            where('sub_category.category_id', '=', $category_id)->
            where('category.resource_type_id', '=', $resource_type_id);

        $collection = ModelUtility::applySearch($collection, $this->table, $search_parameters);

        return $collection->count();
    }

    /**
     * @param integer $resource_type_id
     * @param integer $category_id
     * @param integer $offset
     * @param integer $limit
     * @param array $search_parameters
     * @param array $sort_parameters
     *
     * @return array
     */
    public function paginatedCollection(
        int $resource_type_id,
        int $category_id,
        int $offset = 0,
        int $limit = 10,
        array $search_parameters = [],
        array $sort_parameters = []
    ): array
    {
        $collection = $this->select(
                'sub_category.id AS subcategory_id',
                'sub_category.name AS subcategory_name',
                'sub_category.description AS subcategory_description',
                'sub_category.created_at AS subcategory_created_at'
            )->
            join('category', 'sub_category.category_id', 'category.id')->
            where('sub_category.category_id', '=', $category_id)->
            where('category.resource_type_id', '=', $resource_type_id);

        $collection = ModelUtility::applySearch($collection, $this->table, $search_parameters);

        if (count($sort_parameters) > 0) {
            foreach ($sort_parameters as $field => $direction) {
                switch ($field) {
                    case 'created':
                        $collection->orderBy('sub_category.created_at', $direction);
                        break;

                    default:
                        $collection->orderBy('sub_category.' . $field, $direction);
                        break;
                }
            }
        } else {
            $collection->orderBy('sub_category.name', 'asc');
        }

        $collection->offset($offset)->
            limit($limit);

        return $collection->get()->
            toArray();
    }

    public function single(
        int $category_id,
        int $subcategory_id
    ): ?array
    {
        $result = $this->select(
                'sub_category.id AS subcategory_id',
                'sub_category.name AS subcategory_name',
                'sub_category.description AS subcategory_description',
                'sub_category.created_at AS subcategory_created_at'
            )->
            where('category_id', '=', $category_id)->
            find($subcategory_id);

        if ($result !== null) {
            return $result->toArray();
        } else {
            return null;
        }
    }

    public function instance(
        int $category_id,
        int $subcategory_id
    ): ?Subcategory
    {
        return $this->select(
                'sub_category.id',
                'sub_category.name',
                'sub_category.description'
            )->
            where('category_id', '=', $category_id)->
            find($subcategory_id);
    }

    /**
     * Convert the model instance to an array for use with the transformer
     *
     * @param Subcategory $subcategory
     *
     * @return array
     */
    public function instanceToArray(Subcategory $subcategory): array
    {
        return [
            'subcategory_id' => $subcategory->id,
            'subcategory_name' => $subcategory->name,
            'subcategory_description' => $subcategory->description,
            'subcategory_created_at' => $subcategory->created_at->toDateTimeString()
        ];
    }
}
