<?php
declare(strict_types=1);

namespace App\Models\Summary;

use App\Utilities\Model as ModelUtility;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;

/**
 * Sub category model
 *
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SubCategory extends Model
{
    protected $table = 'sub_category';

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
}
