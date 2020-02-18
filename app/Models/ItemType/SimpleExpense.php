<?php
declare(strict_types=1);

namespace App\Models\ItemType;

use App\Utilities\General;
use App\Utilities\Model as ModelUtility;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Item type model
 *
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SimpleExpense extends Model
{
    protected $table = 'item_type_simple_expense';

    protected $guarded = ['id', 'updated_at', 'created_at'];

    public function instance(int $item_id): ?SimpleExpense
    {
        return $this->where('item_id', '=', $item_id)->
            select(
                'item_type_simple_expense.id'
            )->
            first();
    }

    /**
     * Convert the model instance to an array for use with the transformer
     *
     * @param $item
     * @param Model $item_type
     *
     * @return array
     */
    public function instanceToArray($item, Model $item_type): array
    {
        return [
            'item_id' => $item->id,
            'item_name' => $item_type->name,
            'item_description' => $item_type->description,
            'item_effective_date' => $item_type->effective_date,
            'item_total' => $item_type->total,
            'item_created_at' => $item->created_at->toDateTimeString()
        ];
    }

    /**
     * @param integer $resource_type_id
     * @param integer $resource_id
     * @param integer $item_id
     *
     * @return array|null
     */
    public function single(
        int $resource_type_id,
        int $resource_id,
        int $item_id
    ): ?array
    {
        $result = $this->from('item')->
            join('item_type_simple_expense', 'item.id', 'item_type_simple_expense.item_id')->
            join('resource', 'item.resource_id', 'resource.id')->
            where('item.resource_id', '=', $resource_id)->
            where('resource.resource_type_id', '=', $resource_type_id)->
            where('item_type_simple_expense.item_id', '=', $item_id)->
            where('item.id', '=', $item_id)->
            select(
                'item.id AS item_id',
                'item_type_simple_expense.name AS item_name',
                'item_type_simple_expense.description AS item_description',
                'item_type_simple_expense.effective_date AS item_effective_date',
                'item_type_simple_expense.total AS item_total',
                'item.created_at AS item_created_at'
            )->
            first();

        if ($result !== null) {
            return $result->toArray();
        } else {
            return null;
        }
    }

    /**
     * Return the total count for the given request, similar to the collection
     * method just without the sorting and only returning a count
     *
     * @param integer $resource_type_id
     * @param integer $resource_id
     * @param array $parameters
     * @param array $search_parameters
     *
     * @return integer
     */
    public function totalCount(
        int $resource_type_id,
        int $resource_id,
        array $parameters = [],
        array $search_parameters = []
    ): int
    {
        $collection = $this->from('item')->
            join($this->table, 'item.id', $this->table . '.item_id')->
            join('resource', 'item.resource_id', 'resource.id')->
            where('resource_id', '=', $resource_id)->
            where('resource.resource_type_id', '=', $resource_type_id);

        if (
            array_key_exists('year', $parameters) === true &&
            $parameters['year'] !== null
        ) {
            $collection->whereRaw(DB::raw("YEAR(item_type_simple_expense.effective_date) = '{$parameters['year']}'"));
        }

        if (
            array_key_exists('month', $parameters) === true &&
            $parameters['month'] !== null
        ) {
            $collection->whereRaw(DB::raw("MONTH(item_type_simple_expense.effective_date) = '{$parameters['month']}'"));
        }

        if (
            array_key_exists('category', $parameters) === true &&
            $parameters['category'] !== null
        ) {
            $collection->join("item_category", "item_category.item_id", "item.id");
            $collection->where('item_category.category_id', '=', $parameters['category']);
        }

        if (
            array_key_exists('category', $parameters) === true &&
            $parameters['category'] !== null &&
            array_key_exists('subcategory', $parameters) === true &&
            $parameters['subcategory'] !== null
        ) {
            $collection->join("item_sub_category", "item_sub_category.item_category_id", "item_category.id");
            $collection->where('item_sub_category.sub_category_id', '=', $parameters['subcategory']);
        }

        $collection = ModelUtility::applySearch($collection, 'item_type_simple_expense', $search_parameters);

        return $collection->count();
    }

    /**
     * Work out the maximum effective date year for the requested resource id,
     * defaults to the current year if no data exists
     *
     * @param integer $resource_id
     *
     * @return integer
     */
    public function maximumEffectiveDateYear(int $resource_id): int
    {
        $result = $this->join('item', 'item_type_simple_expense.item_id', 'item.id')->
            where('item.resource_id', '=', $resource_id)->
            selectRaw('YEAR(MAX(`item_type_simple_expense`.`effective_date`)) AS `year_limit`')->
            first();

        if ($result === null) {
            return intval(date('Y'));
        } else {
            return intval($result->year_limit);
        }

    }

    /**
     * Work out the minimum effective date year for the requested resource id,
     * defaults to the current year if no data exists
     *
     * @param integer $resource_id
     *
     * @return integer
     */
    public function minimumEffectiveDateYear(int $resource_id): int
    {
        $result = $this->join('item', 'item_type_simple_expense.item_id', 'item.id')->
            where('item.resource_id', '=', $resource_id)->
            selectRaw('YEAR(MIN(`item_type_simple_expense`.`effective_date`)) AS `year_limit`')->
            first();

        if ($result === null) {
            return intval(date('Y'));
        } else {
            return intval($result->year_limit);
        }
    }

    /**
     * Return the results for the given request based on the supplied parameters
     *
     * @param integer $resource_type_id
     * @param integer $resource_id
     * @param integer $offset
     * @param integer $limit
     * @param array $parameters
     * @param array $sort_parameters
     * @param array $search_parameters
     *
     * @return array
     */
    public function paginatedCollection(
        int $resource_type_id,
        int $resource_id,
        int $offset = 0,
        int $limit = 10,
        array $parameters = [],
        array $sort_parameters = [],
        array $search_parameters = []
    ): array
    {
        $select_fields = [
            'item.id AS item_id',
            'item_type_simple_expense.name AS item_name',
            'item_type_simple_expense.description AS item_description',
            'item_type_simple_expense.effective_date AS item_effective_date',
            'item_type_simple_expense.total AS item_total',
            'item.created_at AS item_created_at'
        ];

        $category_join = false;
        $subcategory_join = false;

        $collection = $this->from('item')->
            join('item_type_simple_expense', 'item.id', 'item_type_simple_expense.item_id')->
            join('resource', 'item.resource_id', 'resource.id')->
            where('resource_id', '=', $resource_id)->
            where('resource.resource_type_id', '=', $resource_type_id);

        if (
            array_key_exists('include-categories', $parameters) === true &&
            General::booleanValue($parameters['include-categories']) === true
        ) {
            $collection->join('item_category', 'item.id', 'item_category.item_id')->
                join('category', 'item_category.category_id', 'category.id');

            $category_join = true;

            $select_fields[] = 'category.id AS category_id';
            $select_fields[] = 'category.name AS category_name';
            $select_fields[] = 'category.description AS category_description';

            if (array_key_exists('category', $parameters) === true &&
                $parameters['category'] !== null) {
                $collection->where('item_category.category_id', '=', $parameters['category']);
            }

            if (
                array_key_exists('include-subcategories', $parameters) === true &&
                General::booleanValue($parameters['include-subcategories']) === true
            ) {
                $collection->join('item_sub_category', 'item_category.id', 'item_sub_category.item_category_id')->
                    join('sub_category', 'item_sub_category.sub_category_id', 'sub_category.id');

                $subcategory_join = true;

                $select_fields[] = 'sub_category.id AS subcategory_id';
                $select_fields[] = 'sub_category.name AS subcategory_name';
                $select_fields[] = 'sub_category.description AS subcategory_description';

                if (array_key_exists('subcategory', $parameters) === true &&
                    $parameters['subcategory'] !== null) {
                    $collection->where('item_sub_category.sub_category_id', '=', $parameters['subcategory']);
                }
            }
        }

        if (array_key_exists('year', $parameters) === true &&
            $parameters['year'] !== null) {
            $collection->whereRaw(DB::raw("YEAR(item_type_simple_expense.effective_date) = '{$parameters['year']}'"));
        }

        if (array_key_exists('month', $parameters) === true &&
            $parameters['month'] !== null) {
            $collection->whereRaw(DB::raw("MONTH(item_type_simple_expense.effective_date) = '{$parameters['month']}'"));
        }

        if (
            array_key_exists('category', $parameters) === true &&
            $parameters['category'] !== null &&
            $category_join === false
        ) {
            $collection->join("item_category", "item_category.item_id", "item.id");
            $collection->where('item_category.category_id', '=', $parameters['category']);
        }

        if (
            array_key_exists('category', $parameters) === true &&
            $parameters['category'] !== null &&
            array_key_exists('subcategory', $parameters) === true &&
            $parameters['subcategory'] !== null &&
            $subcategory_join === false
        ) {
            $collection->join("item_sub_category", "item_sub_category.item_category_id", "item_category.id");
            $collection->where('item_sub_category.sub_category_id', '=', $parameters['subcategory']);
        }

        $collection = ModelUtility::applySearch($collection, 'item_type_simple_expense', $search_parameters);

        if (count($sort_parameters) > 0) {
            foreach ($sort_parameters as $field => $direction) {
                switch ($field) {
                    case 'created':
                        $collection->orderBy('item.created_at', $direction);
                        break;

                    case 'description':
                    case 'effective_date':
                    case 'name':
                    case 'total':
                        $collection->orderBy('item_type_simple_expense.' . $field, $direction);
                        break;

                    default:
                        $collection->orderBy('item.' . $field, $direction);
                        break;
                }
            }
        } else {
            $collection->orderBy('item_type_simple_expense.effective_date', 'desc');
            $collection->orderBy('item.created_at', 'desc');
        }

        $collection->offset($offset);
        $collection->limit($limit);

        return $collection->select($select_fields)->
            get()->
            toArray();
    }
}
