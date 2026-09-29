<?php

declare(strict_types=1);

namespace App\Cache;

use Closure;
use Illuminate\Cache\DatabaseStore;

/**
 * Laravel's DatabaseStore doesn't retry on MySQL deadlocks (SQLSTATE 40001).
 * The rate limiter's add()/increment() calls run on every request, before
 * the controller, so concurrent requests for the same user/IP racing to
 * write the same cache row can deadlock and turn into a 500. Wrapping each
 * write in a retried transaction reuses Laravel's own deadlock detection
 * (Connection::causedByConcurrencyError) instead of reimplementing it.
 *
 * incrementOrDecrement() is reimplemented rather than wrapped: the parent
 * method already opens its own single-attempt transaction, and nesting a
 * retried transaction around it just produces a DeadlockException instead
 * of a clean retry.
 */
class ResilientDatabaseStore extends DatabaseStore
{
    private const RETRY_ATTEMPTS = 3;

    public function add($key, $value, $seconds)
    {
        return $this->connection->transaction(
            fn () => parent::add($key, $value, $seconds),
            self::RETRY_ATTEMPTS
        );
    }

    public function putMany(array $values, $seconds)
    {
        return $this->connection->transaction(
            fn () => parent::putMany($values, $seconds),
            self::RETRY_ATTEMPTS
        );
    }

    protected function incrementOrDecrement($key, $value, Closure $callback)
    {
        return $this->connection->transaction(function () use ($key, $value, $callback) {
            $prefixed = $this->prefix.$key;

            $cache = $this->table()->where('key', $prefixed)
                ->lockForUpdate()->first();

            if (is_null($cache)) {
                return false;
            }

            $cache = is_array($cache) ? (object) $cache : $cache;

            $current = $this->unserialize($cache->value);

            $new = $callback((int) $current, $value);

            if (! is_numeric($current)) {
                return false;
            }

            $this->table()->where('key', $prefixed)->update([
                'value' => $this->serialize($new),
            ]);

            return $new;
        }, self::RETRY_ATTEMPTS);
    }
}
