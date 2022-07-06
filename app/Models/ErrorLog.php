<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * @mixin QueryBuilder
 *
 * @property int $id
 * @property string $message
 * @property string $file
 * @property string $line
 * @property string $trace
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ErrorLog extends Model
{
    protected $table = 'error_log';

    protected $guarded = ['id'];
}
