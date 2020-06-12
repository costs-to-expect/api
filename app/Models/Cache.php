<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;

/**
 * Manage the our cache
 *
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Cache extends Model
{
    protected $table = 'cache';

    protected $guarded = ['key', 'value', 'expiration'];

    /**
     * Fetch all the matching cache keys that are a wildcard match for the
     * given key prefix
     *
     * @param string $key_prefix
     * @param bool $include_summaries
     *
     * @return array
     */
    public function matchingKeys(
        string $key_prefix,
        bool $include_summaries = false
    ): array
    {
        $result = $this->where('key', 'LIKE', $key_prefix . '%')->
            orWhere('key', '=', $key_prefix);

        if ($include_summaries === true) {
            $result->orWhere('key', 'LIKE', str_replace('v2/', 'v2/summary/', $key_prefix) . '%')->
                orWhere('key', '=', str_replace('v2/', 'v2/summary/', $key_prefix));
        }

        return $result->select('key')->
            get()->
            toArray();
    }
}
