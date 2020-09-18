<?php
declare(strict_types=1);

namespace App\Models\ResourceTypeItem;

use App\Interfaces\ResourceTypeItem\IModel;
use App\Request\Validate\Boolean;
use App\Models\Clause;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

/**
 * Item model when fetching data by resource type
 *
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class AllocatedExpense extends Model implements IModel
{
    protected $table = 'item';

    protected $item_table = 'item_type_allocated_expense';

    protected $guarded = ['id', 'actualised_total', 'created_at', 'updated_at'];

    /**
     * Return the total number of items for the requested resource type
     *
     * @param integer $resource_type_id
     * @param array $parameters_collection
     * @param array $search_parameters
     * @param array $filter_parameters
     *
     * @return integer
     */
    public function totalCount(
        int $resource_type_id,
        array $parameters_collection = [],
        array $search_parameters = [],
        array $filter_parameters = []
    ): int
    {
        $collection = $this->join('item_type_allocated_expense', 'item.id', 'item_type_allocated_expense.item_id')->
            join('resource', 'item.resource_id', 'resource.id')->
            join('resource_type', 'resource.resource_type_id', 'resource_type.id')->
            where('resource_type.id', '=', $resource_type_id);

        if (array_key_exists('year', $parameters_collection) === true &&
            $parameters_collection['year'] !== null) {
            $collection->where(DB::raw('YEAR(item_type_allocated_expense.effective_date)'), '=', $parameters_collection['year']);
        }

        if (array_key_exists('month', $parameters_collection) === true &&
            $parameters_collection['month'] !== null) {
            $collection->where(DB::raw('MONTH(item_type_allocated_expense.effective_date)'), '=', $parameters_collection['month']);
        }

        if (array_key_exists('category', $parameters_collection) === true &&
            $parameters_collection['category'] !== null) {
            $collection->join("item_category", "item_category.item_id", "item.id");
            $collection->where('item_category.category_id', '=', $parameters_collection['category']);

            if (
                array_key_exists('subcategory', $parameters_collection) === true &&
                $parameters_collection['subcategory'] !== null
            ) {
                $collection->join('item_sub_category', 'item_category.id', 'item_sub_category.item_category_id')->
                    join('sub_category', 'item_sub_category.sub_category_id', 'sub_category.id')->
                    where('item_sub_category.sub_category_id', '=', $parameters_collection['subcategory']);
            }
        }

        $collection = Clause::applySearch(
            $collection,
            $this->item_table,
            $search_parameters
        );

        $collection = Clause::applyFiltering(
            $collection,
            $this->item_table,
            $filter_parameters
        );

        if (
            array_key_exists('include-unpublished', $parameters_collection) === false ||
            $parameters_collection['include-unpublished'] === false
        ) {
            $collection->where(static function ($collection) {
                $collection->whereNull('item_type_allocated_expense.publish_after')->
                    orWhereRaw('item_type_allocated_expense.publish_after < NOW()');
            });
        }

        return $collection->count();
    }

    /**
     * Return the pagination collection for all the items assigned to the
     * resources for a resource group
     *
     * @param int $resource_type_id
     * @param int $offset
     * @param int $limit
     * @param array $parameters_collection
     * @param array $search_parameters
     * @param array $filter_parameters
     * @param array $sort_parameters
     *
     * @return array
     */
    public function paginatedCollection(
        int $resource_type_id,
        int $offset = 0,
        int $limit = 10,
        array $parameters_collection = [],
        array $search_parameters = [],
        array $filter_parameters = [],
        array $sort_parameters = []
    ): array
    {
        $select_fields = [
            'resource.id AS resource_id',
            'resource.name AS resource_name',
            'resource.description AS resource_description',
            'item.id AS item_id',
            'item_type_allocated_expense.name AS item_name',
            'item_type_allocated_expense.description AS item_description',
            "currency.id AS item_currency_id",
            "currency.code AS item_currency_code",
            "currency.name AS item_currency_name",
            'item_type_allocated_expense.effective_date AS item_effective_date',
            'item_type_allocated_expense.total AS item_total',
            'item_type_allocated_expense.percentage AS item_percentage',
            'item_type_allocated_expense.actualised_total AS item_actualised_total',
            'item_type_allocated_expense.created_at AS item_created_at',
            'item_type_allocated_expense.updated_at AS item_updated_at'
        ];

        $collection = $this->join('item_type_allocated_expense', 'item.id', 'item_type_allocated_expense.item_id')->
            join('resource', 'item.resource_id', 'resource.id')->
            join('resource_type', 'resource.resource_type_id', 'resource_type.id')->
            join('currency', 'item_type_allocated_expense.currency_id', 'currency.id')->
            where('resource_type.id', '=', $resource_type_id);

        $category_join = false; // Check to see if join has taken place
        $subcategory_join = false; // Check to see if join has taken place

        if (
            array_key_exists('include-categories', $parameters_collection) === true &&
            Boolean::convertedValue($parameters_collection['include-categories']) === true
        ) {
            $collection->leftJoin('item_category', 'item.id', 'item_category.item_id')->
                leftJoin('category', 'item_category.category_id', 'category.id');

            $category_join = true;

            $select_fields[] = 'item_category.id AS item_category_id';
            $select_fields[] = 'category.id AS category_id';
            $select_fields[] = 'category.name AS category_name';
            $select_fields[] = 'category.description AS category_description';

            if (array_key_exists('category', $parameters_collection) === true &&
                $parameters_collection['category'] !== null) {
                $collection->where('item_category.category_id', '=', $parameters_collection['category']);
            }

            if (
                array_key_exists('include-subcategories', $parameters_collection) === true &&
                Boolean::convertedValue($parameters_collection['include-subcategories']) === true
            ) {
                $collection->leftJoin('item_sub_category', 'item_category.id', 'item_sub_category.item_category_id')->
                    leftJoin('sub_category', 'item_sub_category.sub_category_id', 'sub_category.id');

                $subcategory_join = true;

                $select_fields[] = 'item_sub_category.id AS item_subcategory_id';
                $select_fields[] = 'sub_category.id AS subcategory_id';
                $select_fields[] = 'sub_category.name AS subcategory_name';
                $select_fields[] = 'sub_category.description AS subcategory_description';

                if (array_key_exists('subcategory', $parameters_collection) === true &&
                    $parameters_collection['subcategory'] !== null) {
                    $collection->where('item_sub_category.sub_category_id', '=', $parameters_collection['subcategory']);
                }
            }
        }

        if (array_key_exists('year', $parameters_collection) === true &&
            $parameters_collection['year'] !== null) {
            $collection->where(DB::raw('YEAR(item_type_allocated_expense.effective_date)'), '=', $parameters_collection['year']);
        }

        if (array_key_exists('month', $parameters_collection) === true &&
            $parameters_collection['month'] !== null) {
            $collection->where(DB::raw('MONTH(item_type_allocated_expense.effective_date)'), '=', $parameters_collection['month']);
        }

        if (array_key_exists('category', $parameters_collection) === true &&
            $parameters_collection['category'] !== null &&
            $category_join === false) {

            $collection->join('item_category', 'item.id', 'item_category.item_id')->
                join('category', 'item_category.category_id', 'category.id')->
                where('item_category.category_id', '=', $parameters_collection['category']);

            if (array_key_exists('subcategory', $parameters_collection) === true &&
                $parameters_collection['subcategory'] !== null &&
                $subcategory_join === false) {

                $collection->join('item_sub_category', 'item_category.id', 'item_sub_category.item_category_id')->
                    join('sub_category', 'item_sub_category.sub_category_id', 'sub_category.id')->
                    where('item_sub_category.sub_category_id', '=', $parameters_collection['subcategory']);
            }
        }

        $collection = Clause::applySearch(
            $collection,
            $this->item_table,
            $search_parameters
        );

        $collection = Clause::applyFiltering(
            $collection,
            $this->item_table,
            $filter_parameters
        );

        if (
            array_key_exists('include-unpublished', $parameters_collection) === false ||
            $parameters_collection['include-unpublished'] === false
        ) {
            $collection->where(static function ($collection) {
                $collection->whereNull('item_type_allocated_expense.publish_after')->
                    orWhereRaw('item_type_allocated_expense.publish_after < NOW()');
            });
        }

        if (count($sort_parameters) > 0) {
            foreach ($sort_parameters as $field => $direction) {
                switch ($field) {
                    case 'created':
                        $collection->orderBy('item.created_at', $direction);
                        break;

                    case 'actualised_total':
                    case 'description':
                    case 'effective_date':
                    case 'name':
                    case 'total':
                        $collection->orderBy('item_type_allocated_expense.' . $field, $direction);
                        break;

                    default:
                        $collection->orderBy('item.' . $field, $direction);
                        break;
                }
            }
        } else {
            $collection->orderBy('item_type_allocated_expense.effective_date', 'desc');
            $collection->orderBy('item.created_at', 'desc');
        }

        $collection->offset($offset);
        $collection->limit($limit);
        $collection->select($select_fields);

        return $collection->get()->toArray();
    }

    /**
     * Work out the maximum effective date year for the requested
     * resource type id, defaults to the current year if no data exists
     *
     * @param integer $resource_type_id
     *
     * @return integer
     */
    public function maximumEffectiveDateYear(int $resource_type_id): int
    {
        $result = $this->from('item_type_allocated_expense')->
            join('item', 'item_type_allocated_expense.item_id', 'item.id')->
            join('resource', 'item.resource_id', 'resource.id')->
            where('resource.resource_type_id', '=', $resource_type_id)->
            selectRaw('YEAR(MAX(`item_type_allocated_expense`.`effective_date`)) AS `year_limit`')->
            first();

        if ($result === null) {
            return (int) date('Y');
        } else {
            return (int) $result->year_limit;
        }
    }

    /**
     * Work out the minimum effective date year for the requested
     * resource type id, defaults to the current year if no data exists
     *
     * @param integer $resource_type_id
     *
     * @return integer
     */
    public function minimumEffectiveDateYear(int $resource_type_id): int
    {
        $result = $this->from('item_type_allocated_expense')->
            join('item', 'item_type_allocated_expense.item_id', 'item.id')->
            join('resource', 'item.resource_id', 'resource.id')->
            where('resource.resource_type_id', '=', $resource_type_id)->
            selectRaw('YEAR(MIN(`item_type_allocated_expense`.`effective_date`)) AS `year_limit`')->
            first();

        if ($result === null) {
            return (int) date('Y');
        } else {
            return (int) $result->year_limit;
        }
    }
}
