<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Request log
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class RequestLog extends Model
{
    protected $table = 'request_log';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function totalCount()
    {
        return count($this->select('id')->get());
    }

    public function paginatedCollection(int $offset = 0, int $limit = 10)
    {
        return $this->orderByDesc('created_at')->offset($offset)->limit($limit)->get();
    }

    public function monthlyRequests()
    {
        $collection = $this->orderBy(DB::raw("DATE_FORMAT(`request_log`.`created_at`, '%Y-%m')"))
            ->groupBy(DB::raw("DATE_FORMAT(`request_log`.`created_at`, '%Y-%m')"))
            ->select(
                DB::raw("DATE_FORMAT(`request_log`.`created_at`, '%Y-%m')"),
                DB::raw("COUNT(`request_log`.`id`) AS `requests`"),
                DB::raw("ANY_VALUE(DATE_FORMAT(`request_log`.`created_at`, '%Y')) AS `year`"),
                DB::raw("ANY_VALUE(DATE_FORMAT(`request_log`.`created_at`, '%M')) AS `month`")
            );

        return $collection->get();
    }
}
