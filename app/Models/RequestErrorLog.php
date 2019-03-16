<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Request error log
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class RequestErrorLog extends Model
{
    protected $table = 'request_error_log';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function totalCount()
    {
        return count($this->select('id')->get());
    }

    public function paginatedCollection(int $offset = 0, int $limit = 10)
    {
        return $this->orderByDesc('created_at')->offset($offset)->limit($limit)->get();
    }
}
