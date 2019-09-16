<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;

/**
 * Item type model
 *
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemTypeAllocatedExpense extends Model
{
    protected $table = 'item_type_allocated_expense';

    protected $guarded = ['id', 'actualised_total'];

    public function setActualisedTotal($total, $percentage)
    {
        $this->attributes['actualised_total'] = ($percentage === 100) ? $total : $total * ($percentage/100);
    }

    public function instance(int $item_id): ?ItemTypeAllocatedExpense
    {
        return $this->where('item_id', '=', $item_id)->
            select(
                'item_type_allocated_expense.id'
            )->
            first();
    }
}
