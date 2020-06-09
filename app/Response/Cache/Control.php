<?php

declare(strict_types=1);

namespace App\Response\Cache;

use Illuminate\Support\Facades\Cache as LaravelCache;
use Illuminate\Support\Facades\Config;

/**
 * Cache helper, wrapper around the Laravel cache facade
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Control
{
    private bool $cacheable = false;

    private string $key_prefix;

    private int $ttl;

    private string $visibility = 'public';

    /**
     * Create an instance of the cache class. The prefix needs to be a
     * string specific to the user, we don't want any unfortunate caching
     * issues
     *
     * @param int $prefix
     */
    public function __construct(int $prefix = null)
    {
        $config = Config::get('api.app.cache');

        $this->ttl = $config['ttl'];

        if ($config['enable'] === true) {
            $this->cacheable = true;
        }

        if ($prefix !== null) {
            $this->key_prefix = '-' . $prefix . '-';
            $this->visibility = 'Private';
        } else {
            $this->key_prefix = $config['public_key_prefix'];
        }
    }

    /**
     * Are we able to cache the request?
     *
     * @return bool
     */
    public function cacheable(): bool
    {
        return $this->cacheable;
    }

    /**
     * Clear any cache entries for the supplied key
     *
     * @param string $key
     *
     * @return bool
     */
    public function clear(string $key): bool
    {
        return LaravelCache::forget($this->key_prefix . $key);
    }

    /**
     * Check the cache to see if there is a stored value for the given key
     *
     * @param string $key
     *
     * @return array|null
     */
    public function get(string $key): ?array
    {
        return LaravelCache::get($this->key_prefix . $key);
    }

    /**
     * Store a new item in cache using the supplied key
     *
     * @param string $key
     * @param array $data
     * @return bool
     */
    public function put(string $key, array $data): bool
    {
        return LaravelCache::put(
            $this->key_prefix . $key,
            $data,
            $this->ttl
        );
    }

    /**
     * Set the ttl for our cache, we default to 1 year, 31536000 seconds,
     * however we can override this at will
     *
     * @param $seconds
     */
    public function setTtl($seconds): void
    {
        $this->ttl = $seconds;
    }

    /**
     * Set the ttl to one year
     */
    public function setTtlOneYear(): void
    {
        $this->setTtl(31536000);
    }

    /**
     * Set the ttl to one month
     */
    public function setTtlOneMonth(): void
    {
        $this->setTtl(2592000);
    }

    /**
     * Set the ttl to one week
     */
    public function setTtlOneWeek(): void
    {
        $this->setTtl(604800);
    }

    /**
     * Set the ttl to one day
     */
    public function setTtlOneDay(): void
    {
        $this->setTtl(86400);
    }

    /**
     * Set the ttl to one hour
     */
    public function setTtlOneHour(): void
    {
        $this->setTtl(3600);
    }

    /**
     * Return the current ttl
     *
     * @return int
     */
    public function ttl(): int
    {
        return $this->ttl;
    }

    /**
     * Return the visibility for the Cache-Control header, depends on where or
     * not a valid Bearer/user id exists
     *
     * @return string
     */
    public function visibility(): string
    {
        return $this->visibility;
    }
}
