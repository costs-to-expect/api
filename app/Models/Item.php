<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Item model
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Item extends Model
{
    protected $table = 'item';

    protected $guarded = ['id', 'actualised_total', 'created_at', 'updated_at'];

    public function setActualisedTotal($total, $percentage)
    {
        $this->attributes['actualised_total'] = ($percentage === 100) ? $total : $total * ($percentage/100);
    }

    public function resource()
    {
        return $this->belongsTo(Resource::class, 'resource_id', 'id');
    }

    public function paginatedCollection(
        int $resource_type_id,
        int $resource_id,
        int $offset = 0,
        int $limit = 10
    )
    {
        return $this->where('resource_id', '=', $resource_id)
            ->whereHas('resource', function ($query) use ($resource_type_id) {
                $query->where('resource_type_id', '=', $resource_type_id);
            })
            ->orderByDesc('effective_date')
            ->latest()
            ->offset($offset)
            ->limit($limit)
            ->get();
    }

    public function single(int $resource_type_id, int $resource_id, int $item_id)
    {
        return $this->where('resource_id', '=', $resource_id)
            ->whereHas('resource', function ($query) use ($resource_type_id) {
                $query->where('resource_type_id', '=', $resource_type_id);
            })
            ->find($item_id);
    }

    public function summary(int $resource_type_id, int $resource_id)
    {
        return $this->where('resource_id', '=', $resource_id)
            ->whereHas('resource', function ($query) use ($resource_type_id) {
                $query->where('resource_type_id', '=', $resource_type_id);
            })
            ->get()
            ->sum('actualised_total');
    }

    public function categoriesSummary(int $resource_type_id, int $resource_id)
    {
        return $this->
            selectRaw("category.id, category.name AS name, SUM(item.actualised_total) AS total")->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            join("item_category", "item_category.item_id", "item.id")->
            join("category", "category.id", "item_category.category_id")->
            where("resource_type.id", "=", $resource_type_id)->
            where("resource.id", "=", $resource_id)->
            groupBy("item_category.category_id")->
            orderBy("name")->
            get();
    }

    public function categorySummary(int $resource_type_id, int $resource_id, $category_id)
    {
        return $this->
            selectRaw("category.id, category.name AS name, SUM(item.actualised_total) AS total")->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            join("item_category", "item_category.item_id", "item.id")->
            join("category", "category.id", "item_category.category_id")->
            where("resource_type.id", "=", $resource_type_id)->
            where("resource.id", "=", $resource_id)->
            where("category.id", "=", $category_id)->
            groupBy("item_category.category_id")->
            orderBy("name")->
            get();
    }

    public function subCategoriesSummary(int $resource_type_id, int $resource_id, int $category_id)
    {
        return $this->
            selectRaw("sub_category.id, sub_category.name AS name, SUM(item.actualised_total) AS total")->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            join("item_category", "item_category.item_id", "item.id")->
            join("item_sub_category", "item_sub_category.item_category_id", "item_category.id")->
            join("category", "category.id", "item_category.category_id")->
            join("sub_category", "sub_category.id", "item_sub_category.sub_category_id")->
            where("resource_type.id", "=", $resource_type_id)->
            where("resource.id", "=", $resource_id)->
            where("category.id", "=", $category_id)->
            groupBy("item_sub_category.sub_category_id")->
            orderBy("name")->
            get();
    }

    public function subCategorySummary(int $resource_type_id, int $resource_id, int $category_id, int $sub_category_id)
    {
        return $this->
            selectRaw("sub_category.id, sub_category.name AS name, SUM(item.actualised_total) AS total")->
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
            groupBy("item_sub_category.sub_category_id")->
            orderBy("name")->
            get();
    }

    public function yearsSummary(int $resource_type_id, int $resource_id)
    {
        return $this->
            selectRaw("YEAR(item.effective_date) as year, SUM(item.actualised_total) AS total")->
            join("resource", "resource.id", "item.resource_id")->
            join("resource_type", "resource_type.id", "resource.resource_type_id")->
            where("resource_type.id", "=", $resource_type_id)->
            where("resource.id", "=", $resource_id)->
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
            groupBy("month")->
            orderBy("month")->
            get();
    }
}
