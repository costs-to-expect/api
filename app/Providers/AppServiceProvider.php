<?php

namespace App\Providers;

use App\Cache\ResilientDatabaseStore;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

        $this->registerResilientDatabaseCacheStore();
    }

    /**
     * Same as Laravel's built-in "database" cache driver, only backed by
     * ResilientDatabaseStore so writes retry on MySQL deadlocks. See
     * ResilientDatabaseStore for why this exists.
     */
    private function registerResilientDatabaseCacheStore(): void
    {
        Cache::extend('resilient_database', function ($app, array $config) {
            $connection = $app['db']->connection($config['connection'] ?? null);

            $store = new ResilientDatabaseStore(
                $connection,
                $config['table'],
                $config['prefix'] ?? $app['config']['cache.prefix'],
                $config['lock_table'] ?? 'cache_locks',
                $config['lock_lottery'] ?? [2, 100],
                $config['lock_timeout'] ?? 86400,
                $app['config']['cache.serializable_classes'] ?? null,
            );

            $store->setLockConnection(
                $app['db']->connection($config['lock_connection'] ?? $config['connection'] ?? null)
            );

            return Cache::repository($store, $config);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
