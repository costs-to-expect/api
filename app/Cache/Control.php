<?php

declare(strict_types=1);

namespace App\Cache;

use App\Models\Cache;
use Illuminate\Support\Facades\Cache as LaravelCache;
use Illuminate\Support\Facades\Config;

/**
 * Cache helper, wrapper around the Laravel cache facade
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Control
{
    private bool $cacheable;

    private int $ttl = 31536000;

    private string $visibility = 'public';

    private string $laravel_cache_prefix;

    private string $cache_prefix;

    private string $public_cache_prefix = '-p-';

    public function __construct(bool $permitted_user = false, int $user_id = null)
    {
        $this->cache_prefix = $this->public_cache_prefix;

        if ($permitted_user === true && $user_id !== null) {
            $this->cache_prefix = '-'  . $user_id . '-';
            $this->visibility = 'private';
        }

        $this->cacheable = (bool) Config::get('api.app.cache.enable');

        $this->laravel_cache_prefix = Config::get('cache.prefix');
    }

    public function isRequestCacheable(): bool
    {
        $skip_cache = request()->header('x-skip-cache');

        if ($skip_cache !== null) {
            return false;
        }

        return $this->cacheable;
    }

    public function clearCacheForRequestedKey(string $key): void
    {
        LaravelCache::forget($this->cache_prefix . $key);

        // Clear public if possibly necessary
        if ($this->cache_prefix !== $this->public_cache_prefix) {
            LaravelCache::forget($this->public_cache_prefix . $key);
        }
    }

    public function clearMatchingPublicCacheKeys(array $key_wildcards): void
    {
        foreach ($key_wildcards as $key_wildcard) {
            $keys = $this->fetchMatchingPublicCacheKeys($key_wildcard);

            foreach ($keys as $key) {
                $this->clearCacheKeyByItsFullName($key['key']);
            }
        }
    }

    public function clearCacheKeyByItsFullName(string $key): void
    {
        // We strip the cache prefix as we went to the db and the prefix will be in the string
        LaravelCache::forget(str_replace_first($this->laravel_cache_prefix, '', $key));
    }

    public function clearMatchingCacheKeys(array $key_wildcards): void
    {
        foreach ($key_wildcards as $key_wildcard) {
            $keys = $this->fetchMatchingCacheKeys($key_wildcard);

            foreach ($keys as $key) {
                $this->clearCacheKeyByItsFullName($key['key']);
            }
        }
    }

    public function getByKey(string $key): ?array
    {
        return LaravelCache::get($this->cache_prefix . $key);
    }

    public function putByKey(string $key, array $data): bool
    {
        return LaravelCache::put(
            $this->cache_prefix . $key,
            $data,
            $this->ttl
        );
    }

    public function setTtl($seconds): void
    {
        $this->ttl = $seconds;
    }

    public function setTtlOneYear(): void
    {
        $this->setTtl(31536000);
    }

    public function setTtlOneMonth(): void
    {
        $this->setTtl(2592000);
    }

    public function setTtlOneWeek(): void
    {
        $this->setTtl(604800);
    }

    public function setTtlOneDay(): void
    {
        $this->setTtl(86400);
    }

    public function setTtlOneHour(): void
    {
        $this->setTtl(3600);
    }

    public function setTtlFivesMinutes(): void
    {
        $this->setTtl(300);
    }

    public function setTtlOneMinute(): void
    {
        $this->setTtl(60);
    }

    public function ttl(): int
    {
        return $this->ttl;
    }

    public function visibility(): string
    {
        return $this->visibility;
    }

    public function fetchMatchingCacheKeys(
        string $key_wildcard
    ): array
    {
        return (new Cache())->matchingKeys(
            $this->laravel_cache_prefix . $this->cache_prefix,
            $key_wildcard
        );
    }

    public function fetchMatchingPublicCacheKeys(
        string $key_wildcard
    ): array
    {
        return (new Cache())->matchingKeys(
            $this->laravel_cache_prefix . $this->public_cache_prefix,
            $key_wildcard
        );
    }
}
