<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * @mixin QueryBuilder
 *
 * @property int $id
 * @property string $method
 * @property string $source
 * @property string $debug
 * @property int $expected_status_code
 * @property int $returned_status_code
 * @property string $request_uri
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class RequestErrorLog extends Model
{
    protected $table = 'request_error_log';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function totalCount(): int
    {
        return $this->count();
    }

    public function paginatedCollection(
        int $offset = 0,
        int $limit = 10
    ): array {
        return $this->select(
            'request_error_log.method AS request_error_log_method',
            'request_error_log.expected_status_code AS request_error_log_expected_status_code',
            'request_error_log.returned_status_code AS request_error_log_returned_status_code',
            'request_error_log.request_uri AS request_error_log_request_uri',
            'request_error_log.source AS request_error_log_source',
            'request_error_log.created_at AS request_error_log_created_at',
            'request_error_log.debug AS request_error_log_debug'
        )
            ->orderByDesc('created_at')
            ->offset($offset)
            ->limit($limit)
            ->get()
            ->toArray();
    }
}
