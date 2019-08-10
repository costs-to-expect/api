<?php

namespace App\Listeners;

use Illuminate\Support\Facades\DB;
use Laravel\Passport\Client;
use Laravel\Passport\Events\AccessTokenCreated;

class PruneTokens
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  AccessTokenCreated  $event
     * @return void
     */
    public function handle(AccessTokenCreated $event)
    {
        DB::table('oauth_access_tokens')->
            where('revoked', '=', 1)->
            where('user_id', '=', $event->userId)->
            delete();
    }
}
