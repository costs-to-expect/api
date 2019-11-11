<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

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

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function totalCount()
    {
       return $this->count();
    }

    /**
     * @param int $offset
     * @param int $limit
     * @param array $collection_parameters
     *
     * @return array
     */
    public function paginatedCollection(
        int $offset = 0,
        int $limit = 10,
        array $collection_parameters = []
    )
    {
        $collection = $this->orderByDesc('id');

        if (array_key_exists('source', $collection_parameters) === true) {
            $collection->where('source', '=', $collection_parameters['source']);
        }

        return $collection->offset($offset)->
            limit($limit)->
            get()->
            toArray();
    }
}
