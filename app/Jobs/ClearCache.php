<?php

namespace App\Jobs;

use App\Response\Cache\Control;
use App\Response\Cache\Job;
use App\Response\Cache\KeyGroup;
use App\Response\Cache\Trash;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Clear the requested cache keys
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ClearCache implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $payload;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $payload = new Job($this->payload);

        $cache_control = new Control(
            $payload->cachePrefix(),
            $payload->permittedUser()
        );

        $cache_key_group = new KeyGroup($payload->parameters());

        $trash = new Trash(
            $cache_control,
            $cache_key_group->keys($payload->groupKey()),
            $payload->parameters()['resource_type_id'],
            $payload->publicResourceTypes(),
            $payload->permittedUsers()
        );

        $trash->all();
    }
}
