<?php
declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

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
     * Fetch all the matching private cache keys that are a match or wildcard
     * match for the given key and prefix, optionally we can fetch summary
     * cache keys
     *
     * @param string $prefix
     * @param string $key
     * @param bool $include_summaries
     *
     * @return array
     */
    public function matchingKeys(
        string $prefix,
        string $key,
        bool $include_summaries = false
    ): array
    {
        $result = $this->where('expiration', '>', DB::raw('UNIX_TIMESTAMP()'))
            ->where('key', 'LIKE', $prefix . $key . '%')
            ->orWhere('key', '=', $prefix . $key);

        if ($include_summaries === true) {
            $result->orWhere('key', 'LIKE', str_replace('v2/', 'v2/summary/', $prefix . $key) . '%')
                ->orWhere('key', '=', str_replace('v2/', 'v2/summary/', $prefix . $key));
        }

        return $result->select('key')
            ->get()
            ->toArray();
    }
}
