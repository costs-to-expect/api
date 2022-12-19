<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * @mixin QueryBuilder
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
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
     *
     * @return array
     */
    public function matchingKeys(
        string $prefix,
        string $key
    ): array {
        $key = $prefix . rtrim($key, '/');
        $summary_key = rtrim(str_replace('v3/', 'v3/summary/', $key), '/');

        $result = $this
            ->where(static function ($query) use ($key, $summary_key) {
                $query->where('key', 'LIKE', $key . '%')
                    ->orWhere('key', 'LIKE', $summary_key . '%');
            });

        return $result->select('key')
            ->get()
            ->toArray();
    }
}
