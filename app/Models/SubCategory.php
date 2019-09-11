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
class SubCategory extends Model
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

        $collection = ModelUtility::applySearch($collection, $this->table, $search_parameters);

        return count($collection->get());
    }

    /**
     * @param integer $category_id
     * @param integer $offset
     * @param integer $limit
     * @param array $search_parameters
     * @param array $sort_parameters
     *
     * @return array
     */
    public function paginatedCollection(
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
            where('category_id', '=', $category_id);

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

    public function instance(
        int $category_id,
        int $sub_category_id
    ): ?SubCategory
    {
        return $this->select(
                'sub_category.id',
                'sub_category.name',
                'sub_category.description'
            )->
            where('category_id', '=', $category_id)->
            find($sub_category_id);
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

    /**
     * Validate that the subcategory exists and is accessible to the user for
     * viewing, editing based on their permitted resource types
     *
     * @param integer $category_id
     * @param integer $subcategory_id
     * @param array $permitted_resource_types
     * @param boolean $manage Should be exclude public items as we are checking
     * a management route
     *
     * @return boolean
     */
    public function existsToUser(
        int $category_id,
        int $subcategory_id,
        array $permitted_resource_types,
        $manage = false
    ): bool
    {
        $collection = $this->where('sub_category.id', '=', $subcategory_id)->
            join('category', 'sub_category.category_id', 'category.id')->
            join('resource_type', 'category.resource_type_id', 'resource_type.id')->
            where('sub_category.category_id', '=', $category_id);

        $collection = ModelUtility::applyResourceTypeCollectionCondition(
            $collection,
            $permitted_resource_types,
            ($manage === true) ? false : true
        );

        return (count($collection->get()) === 1) ? true : false;
    }
}
