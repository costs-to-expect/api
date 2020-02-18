<?php
declare(strict_types=1);

namespace App\Models\Summary;

use App\Utilities\Model as ModelUtility;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;

/**
 * Category model
 *
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Category extends Model
{
    protected $table = 'category';

    /**
     * @param integer $resource_type_id
     * @param array $permitted_resource_types
     * @param boolean $include_public
     * @param array $search_parameters
     *
     * @return integer
     */
    public function total(
        int $resource_type_id,
        array $permitted_resource_types,
        bool $include_public,
        array $search_parameters = []
    ): int
    {
        $collection = $this->select('category.id')->
            join("resource_type", "category.resource_type_id", "resource_type.id")->
            where('category.resource_type_id', '=', $resource_type_id);

        $collection = ModelUtility::applyResourceTypeCollectionCondition(
            $collection,
            $permitted_resource_types,
            $include_public
        );

        $collection = ModelUtility::applySearch($collection, $this->table, $search_parameters);

        return $collection->count();
    }
}
