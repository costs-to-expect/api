<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Request log
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class RequestLog extends Model
{
    protected $table = 'request_log';

    protected $guarded = ['id', 'created_at', 'updated_at'];
}
