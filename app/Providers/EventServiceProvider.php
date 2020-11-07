<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Laravel\Passport\Events\AccessTokenCreated' => [
            'App\Listeners\RevokeTokens',
            'App\Listeners\PruneTokens'
        ],
        'App\Events\RequestError' => [
            'App\Listeners\CaptureAndSendRequestError'
        ],
        'App\Events\InternalError' => [
            'App\Listeners\CaptureAndSendInternalError'
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
