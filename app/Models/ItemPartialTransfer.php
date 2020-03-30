<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;

/**
 * Item type model
 *
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemPartialTransfer extends Model
{
    protected $table = 'item_partial_transfer';

    protected $guarded = ['id', 'created_at', 'updated_at'];
}
