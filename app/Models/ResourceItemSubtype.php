<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceItemSubtype extends Model
{
    protected $table = 'resource_item_subtype';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function instance(int $resource_id): Model
    {
        return $this
            ->where('resource_id', '=', $resource_id)
            ->first();
    }
}
