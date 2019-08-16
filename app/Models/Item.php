<?php
declare(strict_types=1);

namespace App\Models;

use App\Utilities\General;
use App\Utilities\Model as ModelUtility;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

/**
 * Item model
 *
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Item extends Model
{
    protected $table = 'item';

    protected $guarded = ['id', 'actualised_total', 'created_at', 'updated_at'];

    /**
     * Return an array of the fields that can be PATCHed.
     *
     * @return array
     */
    public function patchableFields(): array
    {
        return array_keys(Config::get('api.item.validation.PATCH.fields'));
    }

    public function setActualisedTotal($total, $percentage)
    {
        $this->attributes['actualised_total'] = ($percentage === 100) ? $total : $total * ($percentage/100);
    }

    public function resource()
    {
        return $this->belongsTo(Resource::class, 'resource_id', 'id');
    }

    /**
     * Return the total count for the given request, similar to the collection method just
     * without the sorting and only returning a count
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
        $collection = $this->where('resource_id', '=', $resource_id)
            ->join('resource', 'item.resource_id', 'resource.id')
            ->where('resource.resource_type_id', '=', $resource_type_id);

        if (
            array_key_exists('year', $parameters) === true &&
            $parameters['year'] !== null
        ) {
            $collection->whereRaw(DB::raw("YEAR(item.effective_date) = '{$parameters['year']}'"));
        }

        if (
            array_key_exists('month', $parameters) === true &&
            $parameters['month'] !== null
        ) {
            $collection->whereRaw(DB::raw("MONTH(item.effective_date) = '{$parameters['month']}'"));
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

        $collection = ModelUtility::applySearch($collection, $this->table, $search_parameters);

        if (
            array_key_exists('include-unpublished', $parameters) === false ||
            General::booleanValue($parameters['include-unpublished']) === false
        ) {
            $collection->where(function ($collection) {
                $collection->whereNull('item.publish_after')->orWhereRaw('item.publish_after < NOW()');
            });
        }

        return count($collection->get());
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
            'item.description AS item_description',
            'item.effective_date AS item_effective_date',
            'item.total AS item_total',
            'item.percentage AS item_percentage',
            'item.actualised_total AS item_actualised_total',
            'item.created_at AS item_created_at'
        ];

        $category_join = false;
        $subcategory_join = false;

        $collection = $this->where('resource_id', '=', $resource_id)
            ->join('resource', 'item.resource_id', 'resource.id')
            ->where('resource.resource_type_id', '=', $resource_type_id);

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
            $collection->whereRaw(\DB::raw("YEAR(item.effective_date) = '{$parameters['year']}'"));
        }

        if (array_key_exists('month', $parameters) === true &&
            $parameters['month'] !== null) {
            $collection->whereRaw(\DB::raw("MONTH(item.effective_date) = '{$parameters['month']}'"));
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

        $collection = ModelUtility::applySearch($collection, $this->table, $search_parameters);

        if (
            array_key_exists('include-unpublished', $parameters) === false ||
            General::booleanValue($parameters['include-unpublished']) === false
        ) {
            $collection->where(function ($collection) {
                $collection->whereNull('item.publish_after')->orWhereRaw('item.publish_after < NOW()');
            });
        }

        if (count($sort_parameters) > 0) {
            foreach ($sort_parameters as $field => $direction) {
                switch ($field) {
                    case 'created':
                        $collection->orderBy('item.created_at', $direction);
                        break;

                    default:
                        $collection->orderBy('item.' . $field, $direction);
                        break;
                }
            }
        } else {
            $collection->orderBy('item.effective_date', 'desc');
            $collection->orderBy('item.created_at', 'desc');
        }

        $collection->offset($offset);
        $collection->limit($limit);

        return $collection->select($select_fields)->
            get()->
            toArray();
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
        $result = $this->where('resource_id', '=', $resource_id)->
            join('resource', 'item.resource_id', 'resource.id')->
            where('resource.resource_type_id', '=', $resource_type_id)->
            select(
                'item.id AS item_id',
                'item.description AS item_description',
                'item.effective_date AS item_effective_date',
                'item.total AS item_total',
                'item.percentage AS item_percentage',
                'item.actualised_total AS item_actualised_total',
                'item.created_at AS item_created_at'
            )
            ->find($item_id);

        if ($result !== null) {
            return $result->toArray();
        } else {
            return null;
        }
    }

    public function instance(
        int $resource_type_id,
        int $resource_id,
        int $item_id
    ): ?Item
    {
        return $this->where('resource_id', '=', $resource_id)->
            join('resource', 'item.resource_id', 'resource.id')->
            where('resource.resource_type_id', '=', $resource_type_id)->
            select(
                'item.id',
                'item.description',
                'item.effective_date',
                'item.publish_after',
                'item.total',
                'item.percentage',
                'item.actualised_total',
                'item.created_at'
            )
            ->find($item_id);
    }

    /**
     * Convert the model instance to an array for use with the transformer
     *
     * @param Item
     *
     * @return array
     */
    public function instanceToArray(Item $item): array
    {
        return [
            'item_id' => $item->id,
            'item_description' => $item->description,
            'item_effective_date' => $item->effective_date,
            'item_publish_after' => $item->publish_after,
            'item_total' => $item->total,
            'item_percentage' => $item->percentage,
            'item_actualised_total' => $item->item_actualised_total,
            'item_created_at' => $item->created_at->toDateTimeString()
        ];
    }
}
