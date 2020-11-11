<?php
declare(strict_types=1);

namespace App\Models\ResourceTypeItem\Summary;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * Item model when fetching data by resource type
 *
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SimpleItem extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $table = 'item';
    protected $sub_table = 'item_type_simple_item';

    /**
     * Return the summary for all items for the resources in the requested resource type
     *
     * @param int $resource_type_id
     * @param array $parameters
     *
     * @return array
     */
    public function summary(
        int $resource_type_id,
        array $parameters
    ): array
    {
        $collection = $this
            ->selectRaw("
                SUM({$this->sub_table}.quantity) AS total, 
                COUNT({$this->sub_table}.item_id) AS total_count, 
                MAX({$this->sub_table}.created_at) AS last_updated
            ")
            ->join($this->sub_table, 'item.id', "{$this->sub_table}.item_id")
            ->join('resource', 'item.resource_id', 'resource.id')
            ->join('resource_type', 'resource.resource_type_id', 'resource_type.id')
            ->where('resource_type.id', '=', $resource_type_id);

        return $collection
            ->get()
            ->toArray();
    }

    /**
     * Return the summary for all items for the resources in the requested resource
     * type grouped by resource
     *
     * @param int $resource_type_id
     * @param array $parameters
     *
     * @return array
     */
    public function resourcesSummary(
        int $resource_type_id,
        array $parameters
    ): array
    {
        $collection = $this
            ->selectRaw("
                resource.id AS id, 
                resource.name AS `name`, 
                SUM({$this->sub_table}.quantity) AS total, 
                COUNT({$this->sub_table}.item_id) AS total_count, 
                MAX({$this->sub_table}.created_at) AS last_updated"
            )
            ->join($this->sub_table, 'item.id', "{$this->sub_table}.item_id")
            ->join('resource', 'item.resource_id', 'resource.id')
            ->join('resource_type', 'resource.resource_type_id', 'resource_type.id')
            ->where('resource_type.id', '=', $resource_type_id);

        return $collection
            ->groupBy('resource.id')
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    /**
     * @param int $resource_type_id
     * @param int|null $category_id
     * @param int|null $subcategory_id
     * @param int|null $year
     * @param int|null $month
     * @param array $parameters
     * @param array $search_parameters
     * @return array
     */
    public function filteredSummary(
        int $resource_type_id,
        array $parameters = [],
        array $search_parameters = []
    ): array
    {
        $collection = $this
            ->selectRaw("
                SUM({$this->sub_table}.quantity) AS total, 
                COUNT({$this->sub_table}.item_id) AS total_count, 
                MAX({$this->sub_table}.created_at) AS last_updated
            ")
            ->join($this->sub_table, 'item.id', "{$this->sub_table}.item_id")
            ->join("resource", "resource.id", "item.resource_id")
            ->join("resource_type", "resource_type.id", "resource.resource_type_id")
            ->where("resource_type.id", "=", $resource_type_id);

        if (count($search_parameters) > 0) {
            foreach ($search_parameters as $field => $search_term) {
                $collection->where("{$this->sub_table}." . $field, 'LIKE', '%' . $search_term . '%');
            }
        }

        return $collection
            ->get()
            ->toArray();
    }
}
