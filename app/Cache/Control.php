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
 * @copyright Dean Blackborough 2018-2023
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Control
{
    /**
     * @var bool Should we cache responses? Defaults to whatever the
     * APP_CACHE env variable is set to, can be overridden on a request with
     * the X-Skip-Cache header
     */
    private bool $cache_responses;

    private int $ttl = 31536000;

    private string $visibility = 'public';

    private string $laravel_cache_prefix;

    private string $cache_prefix;

    private string $public_cache_prefix = '-p-';

    public function __construct(int $user_id = null)
    {
        $this->cache_prefix = $this->public_cache_prefix;

        if ($user_id !== null) {
            $this->cache_prefix = '-' . $user_id . '-';
            $this->visibility = 'private';
        }

        $this->cache_responses = (bool) Config::get('api.app.cache.enable');

        $this->laravel_cache_prefix = Config::get('cache.prefix');
    }

    public function isRequestCacheable(): bool
    {
        // Cacheable defaults to the ENV setting, the X-Skip-Cache header allows overriding of the setting
        $skip_cache = request()->header('X-Skip-Cache');

        if ($skip_cache !== null) {
            return false;
        }

        return $this->cache_responses;
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

    protected function clearCacheKeyByItsFullName(string $key): void
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

    protected function fetchMatchingCacheKeys(
        string $key_wildcard
    ): array {
        return (new Cache())->matchingKeys(
            $this->laravel_cache_prefix . $this->cache_prefix,
            $key_wildcard
        );
    }

    protected function fetchMatchingPublicCacheKeys(
        string $key_wildcard
    ): array {
        return (new Cache())->matchingKeys(
            $this->laravel_cache_prefix . $this->public_cache_prefix,
            $key_wildcard
        );
    }
}
