<?php
declare(strict_types=1);

namespace App\Models\Summary;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

/**
 * Request log
 *
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class RequestLog extends Model
{
    protected $table = 'request_log';

    /**
     * @param array $collection_parameters
     *
     * @return array
     */
    public function monthlyRequests(array $collection_parameters = []): array
    {
        $collection = $this->orderBy(DB::raw("DATE_FORMAT(`request_log`.`created_at`, '%Y-%m')"))
            ->groupBy(DB::raw("DATE_FORMAT(`request_log`.`created_at`, '%Y-%m')"));

        if (array_key_exists('source', $collection_parameters) === true) {
            $collection->where("source", '=', $collection_parameters['source']);
        }

        $collection->select(
            DB::raw("DATE_FORMAT(`request_log`.`created_at`, '%Y-%m')"),
            DB::raw("COUNT(`request_log`.`id`) AS `requests`"),
            DB::raw("ANY_VALUE(DATE_FORMAT(`request_log`.`created_at`, '%Y')) AS `year`"),
            DB::raw("ANY_VALUE(DATE_FORMAT(`request_log`.`created_at`, '%M')) AS `month`")
        );

        return $collection->get()->toArray();
    }
}
