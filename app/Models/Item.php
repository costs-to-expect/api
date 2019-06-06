<?php
declare(strict_types=1);

namespace App\Models;

use App\Utilities\General;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

/**
 * Item model
 *
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

    public function totalCount(
        int $resource_type_id,
        int $resource_id,
        array $parameters_collection = []
    )
    {
        $collection = $this->where('resource_id', '=', $resource_id)
            ->whereHas('resource', function ($query) use ($resource_type_id) {
                $query->where('resource_type_id', '=', $resource_type_id);
            });

        if (array_key_exists('year', $parameters_collection) === true &&
            $parameters_collection['year'] !== null) {
            $collection->whereRaw(\DB::raw("YEAR(item.effective_date) = '{$parameters_collection['year']}'"));
        }

        if (array_key_exists('month', $parameters_collection) === true &&
            $parameters_collection['month'] !== null) {
            $collection->whereRaw(\DB::raw("MONTH(item.effective_date) = '{$parameters_collection['month']}'"));
        }

        if (array_key_exists('category', $parameters_collection) === true &&
            $parameters_collection['category'] !== null) {
            $collection->join("item_category", "item_category.item_id", "item.id");
            $collection->where('item_category.category_id', '=', $parameters_collection['category']);
        }

        if (
            array_key_exists('category', $parameters_collection) === true &&
            $parameters_collection['category'] !== null &&
            array_key_exists('subcategory', $parameters_collection) === true &&
            $parameters_collection['subcategory'] !== null
        ) {
            $collection->join("item_sub_category", "item_sub_category.item_category_id", "item_category.id");
            $collection->where('item_sub_category.sub_category_id', '=', $parameters_collection['subcategory']);
        }

        $collection->whereNull('item.publish_after')->
            orWhereRaw('item.publish_after < NOW()');

        return count($collection->get());
    }

    public function paginatedCollection(
        int $resource_type_id,
        int $resource_id,
        int $offset = 0,
        int $limit = 10,
        array $parameters_collection = []
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

        $collection = $this->where('resource_id', '=', $resource_id)
            ->whereHas('resource', function ($query) use ($resource_type_id) {
                $query->where('resource_type_id', '=', $resource_type_id);
            })
            ->offset($offset)
            ->limit($limit);

        if (
            array_key_exists('include-categories', $parameters_collection) === true &&
            General::booleanValue($parameters_collection['include-categories']) === true
        ) {
            $collection->join('item_category', 'item.id', 'item_category.item_id')->
                join('category', 'item_category.category_id', 'category.id');

            $select_fields[] = 'category.id AS category_id';
            $select_fields[] = 'category.name AS category_name';
            $select_fields[] = 'category.description AS category_description';

            if (array_key_exists('category', $parameters_collection) === true &&
                $parameters_collection['category'] !== null) {
                $collection->where('item_category.category_id', '=', $parameters_collection['category']);
            }

            if (
                array_key_exists('include-subcategories', $parameters_collection) === true &&
                General::booleanValue($parameters_collection['include-subcategories']) === true
            ) {
                $collection->join('item_sub_category', 'item_category.id', 'item_sub_category.item_category_id')->
                    join('sub_category', 'item_sub_category.sub_category_id', 'sub_category.id');

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
            $collection->whereRaw(\DB::raw("YEAR(item.effective_date) = '{$parameters_collection['year']}'"));
        }

        if (array_key_exists('month', $parameters_collection) === true &&
            $parameters_collection['month'] !== null) {
            $collection->whereRaw(\DB::raw("MONTH(item.effective_date) = '{$parameters_collection['month']}'"));
        }

        if (array_key_exists('category', $parameters_collection) === true &&
            $parameters_collection['category'] !== null) {
            $collection->join("item_category", "item_category.item_id", "item.id");
            $collection->where('item_category.category_id', '=', $parameters_collection['category']);
        }

        if (
            array_key_exists('category', $parameters_collection) === true &&
            $parameters_collection['category'] !== null &&
            array_key_exists('subcategory', $parameters_collection) === true &&
            $parameters_collection['subcategory'] !== null
        ) {
            $collection->join("item_sub_category", "item_sub_category.item_category_id", "item_category.id");
            $collection->where('item_sub_category.sub_category_id', '=', $parameters_collection['subcategory']);
        }

        if (array_key_exists('sort', $parameters_collection) === true) {
            $sorting_parameters = explode('|', $parameters_collection['sort']);

            if (count($sorting_parameters) > 0) {
                foreach ($sorting_parameters as $sort) {
                    $sort = explode(':', $sort);

                    if (
                        is_array($sort) === true &&
                        count($sort) === 2 &&
                        in_array($sort[1], ['asc', 'desc']) === true &&
                        in_array($sort[0], ["description", "total", "actualised_total", "effective_date", "created"]) === true
                    ) {
                        switch ($sort[0]) {
                            case 'description':
                            case 'total':
                            case 'actualised_total':
                            case 'effective_date':
                                $collection->orderBy($sort[0], $sort[1]);
                                break;

                            case 'created':
                                $collection->orderBy('created_at', $sort[1]);
                                break;

                            default:
                                break;
                        }
                    }
                }
            }
        } else {
            $collection->orderBy('item.effective_date', 'desc');
            $collection->orderBy('item.created_at', 'desc');
        }

        $collection->whereNull('item.publish_after')->
            orWhereRaw('item.publish_after < NOW()');

        return $collection->select($select_fields)->get()->toArray();
    }

    public function single(int $resource_type_id, int $resource_id, int $item_id): array
    {
        return $this->where('resource_id', '=', $resource_id)
            ->whereHas('resource', function ($query) use ($resource_type_id) {
                $query->where('resource_type_id', '=', $resource_type_id);
            })
            ->select(
                'item.id AS item_id',
                'item.description AS item_description',
                'item.effective_date AS item_effective_date',
                'item.total AS item_total',
                'item.percentage AS item_percentage',
                'item.actualised_total AS item_actualised_total',
                'item.created_at AS item_created_at'
            )
            ->find($item_id)
            ->toArray();
    }

    public function instance(int $resource_type_id, int $resource_id, int $item_id)
    {
        return $this->where('resource_id', '=', $resource_id)
            ->whereHas('resource', function ($query) use ($resource_type_id) {
                $query->where('resource_type_id', '=', $resource_type_id);
            })
            ->select(
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
     * Return the summary for items
     *
     * @param int $resource_type_id
     * @param int $resource_id
     *
     * @return array
     */
    public function summary(int $resource_type_id, int $resource_id): array
    {
        return $this->selectRaw('sum(item.actualised_total) AS actualised_total')
            ->where('resource_id', '=', $resource_id)
            ->whereHas('resource', function ($query) use ($resource_type_id) {
                $query->where('resource_type_id', '=', $resource_type_id);
            })
            ->whereNull('item.publish_after')
            ->orWhereRaw('item.publish_after < NOW()')
            ->get()
            ->toArray();
    }

    /**
     * Return the summary of items, grouped by category
     *
     * @param int $resource_type_id
     * @param int $resource_id
     * @return mixed
     */
    public function categoriesSummary(int $resource_type_id, int $resource_id): array
    {
        return $this->
            selectRaw('
                category.id, 
                category.name AS name, 
                category.description AS description,
                SUM(item.actualised_total) AS total')->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            join("item_category", "item_category.item_id", "item.id")->
            join("category", "category.id", "item_category.category_id")->
            where("category.resource_type_id", "=", $resource_type_id)->
            where("resource_type.id", "=", $resource_type_id)->
            where("resource.id", "=", $resource_id)->
            whereNull('item.publish_after')->
            orWhereRaw('item.publish_after < NOW()')->
            groupBy("item_category.category_id")->
            orderBy("name")->
            get()->
            toArray();
    }

    public function categorySummary(int $resource_type_id, int $resource_id, $category_id): array
    {
        return $this->
            selectRaw('
                category.id, 
                category.name AS name, 
                category.description AS description, 
                SUM(item.actualised_total) AS total')->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            join("item_category", "item_category.item_id", "item.id")->
            join("category", "category.id", "item_category.category_id")->
            where("category.resource_type_id", "=", $resource_type_id)->
            where("resource_type.id", "=", $resource_type_id)->
            where("resource.id", "=", $resource_id)->
            where("category.id", "=", $category_id)->
            whereNull('item.publish_after')->
            orWhereRaw('item.publish_after < NOW()')->
            groupBy("item_category.category_id")->
            orderBy("name")->
            get()->
            toArray();
    }

    public function subCategoriesSummary(int $resource_type_id, int $resource_id, int $category_id): array
    {
        return $this->
            selectRaw('
                sub_category.id, 
                sub_category.name AS name, 
                sub_category.description AS description,
                SUM(item.actualised_total) AS total')->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            join("item_category", "item_category.item_id", "item.id")->
            join("item_sub_category", "item_sub_category.item_category_id", "item_category.id")->
            join("category", "category.id", "item_category.category_id")->
            join("sub_category", "sub_category.id", "item_sub_category.sub_category_id")->
            where("resource_type.id", "=", $resource_type_id)->
            where("resource.id", "=", $resource_id)->
            where("category.id", "=", $category_id)->
            whereNull('item.publish_after')->
            orWhereRaw('item.publish_after < NOW()')->
            groupBy("item_sub_category.sub_category_id")->
            orderBy("name")->
            get()->
            toArray();
    }

    public function subCategorySummary(
        int $resource_type_id,
        int $resource_id,
        int $category_id,
        int $sub_category_id
    ): array
    {
        return $this->
            selectRaw('
                sub_category.id, 
                sub_category.name AS name, 
                sub_category.description AS description,
                SUM(item.actualised_total) AS total')->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            join("item_category", "item_category.item_id", "item.id")->
            join("item_sub_category", "item_sub_category.item_category_id", "item_category.id")->
            join("category", "category.id", "item_category.category_id")->
            join("sub_category", "sub_category.id", "item_sub_category.sub_category_id")->
            where("resource_type.id", "=", $resource_type_id)->
            where("resource.id", "=", $resource_id)->
            where("category.id", "=", $category_id)->
            where("sub_category.id", "=", $sub_category_id)->
            whereNull('item.publish_after')->
            orWhereRaw('item.publish_after < NOW()')->
            groupBy("item_sub_category.sub_category_id")->
            orderBy("name")->
            get()->
            toArray();
    }

    public function yearsSummary(int $resource_type_id, int $resource_id)
    {
        return $this->
            selectRaw("YEAR(item.effective_date) as year, SUM(item.actualised_total) AS total")->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            where("resource_type.id", "=", $resource_type_id)->
            where("resource.id", "=", $resource_id)->
            whereNull('item.publish_after')->
            orWhereRaw('item.publish_after < NOW()')->
            groupBy("year")->
            orderBy("year")->
            get();
    }

    public function yearSummary(int $resource_type_id, int $resource_id, int $year)
    {
        return $this->
            selectRaw("YEAR(item.effective_date) as year, SUM(item.actualised_total) AS total")->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            where("resource_type.id", "=", $resource_type_id)->
            where("resource.id", "=", $resource_id)->
            whereRaw(\DB::raw("YEAR(item.effective_date) = '{$year}'"))->
            whereNull('item.publish_after')->
            orWhereRaw('item.publish_after < NOW()')->
            groupBy("year")->
            get();
    }

    public function monthsSummary(int $resource_type_id, int $resource_id, int $year)
    {
        return $this->
            selectRaw("MONTH(item.effective_date) as month, SUM(item.actualised_total) AS total")->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            where("resource_type.id", "=", $resource_type_id)->
            where("resource.id", "=", $resource_id)->
            whereRaw(\DB::raw("YEAR(item.effective_date) = '{$year}'"))->
            whereNull('item.publish_after')->
            orWhereRaw('item.publish_after < NOW()')->
            groupBy("month")->
            orderBy("month")->
            get();
    }

    public function monthSummary(int $resource_type_id, int $resource_id, int $year, int $month)
    {
        return $this->
            selectRaw("MONTH(item.effective_date) as month, SUM(item.actualised_total) AS total")->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            where("resource_type.id", "=", $resource_type_id)->
            where("resource.id", "=", $resource_id)->
            whereRaw(\DB::raw("YEAR(item.effective_date) = '{$year}'"))->
            whereRaw(\DB::raw("MONTH(item.effective_date) = '{$month}'"))->
            whereNull('item.publish_after')->
            orWhereRaw('item.publish_after < NOW()')->
            groupBy("month")->
            orderBy("month")->
            get();
    }
}
