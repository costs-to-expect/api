<?php

namespace App\Jobs;

use App\Models\ResourceType;
use App\Models\ResourceTypeAccess;
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
            $payload->userId(),
            $payload->permittedUser()
        );

        $cache_key_group = new KeyGroup($payload->routeParameters());

        $permitted_users = (new ResourceTypeAccess())->permittedResourceTypeUsers(
            $payload->routeParameters()['resource_type_id'],
            $payload->userId()
        );

        $public_resource_types = (new ResourceType())->publicResourceTypes();

        $trash = new Trash(
            $cache_control,
            $cache_key_group->keys($payload->groupKey()),
            $payload->routeParameters()['resource_type_id'],
            $public_resource_types,
            $permitted_users
        );

        $trash->all();
    }
}
