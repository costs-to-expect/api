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
     * given key prefix, we look for private and public vrsions of the cache
     *
     * @param string $public_prefix
     * @param string $key_wildcard
     * @param string|null $private_prefix
     * @param bool $include_summaries
     *
     * @return array
     */
    public function matchingKeys(
        string $public_prefix,
        string $key_wildcard,
        string $private_prefix = null,
        bool $include_summaries = false
    ): array
    {
        $result = $this->where('key', 'LIKE', $public_prefix . $key_wildcard . '%')->
            orWhere('key', '=', $public_prefix . $key_wildcard);

        if ($private_prefix !== null) {
            $result->orWhere('key', 'LIKE', $private_prefix . $key_wildcard . '%')->
                orWhere('key', '=', $private_prefix . $key_wildcard);
        }

        if ($include_summaries === true) {
            $result->orWhere('key', 'LIKE', str_replace('v2/', 'v2/summary/', $public_prefix . $key_wildcard) . '%')->
                orWhere('key', '=', str_replace('v2/', 'v2/summary/', $public_prefix . $key_wildcard));

            if ($private_prefix !== null) {
                $result->orWhere('key', 'LIKE', str_replace('v2/', 'v2/summary/', $private_prefix . $key_wildcard) . '%')
                    ->orWhere('key', '=', str_replace('v2/', 'v2/summary/', $private_prefix . $key_wildcard));
            }
        }

        return $result->select('key')->
            get()->
            toArray();
    }
}
