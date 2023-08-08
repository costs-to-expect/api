<?php

namespace App\Jobs;

use App\Cache\Control;
use App\Cache\KeyGroup;
use App\Models\Permission;
use App\Models\ResourceType;
use App\Notifications\FailedJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;
use Throwable;

/**
 * Clear the requested cache keys
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2023
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ClearCache implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected array $payload;

    public int $tries = 3;

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
        $cache_control = new Control($this->payload['user_id']);

        $cache_key_group = new KeyGroup($this->payload['route_parameters']);
        $cache_keys = $cache_key_group->keys($this->payload['group_key']);

        if (array_key_exists('resource_type_id', $this->payload['route_parameters'])) {
            $permitted_users = (new Permission())->permittedUsersForResourceType(
                $this->payload['route_parameters']['resource_type_id'],
                $this->payload['user_id']
            );

            $public_resource_types = (new ResourceType())->publicResourceTypes();

            $cache_control->clearMatchingCacheKeys($cache_keys);

            foreach ($permitted_users as $permitted_user) {
                $cache_control_for_permitted_user = new Control($permitted_user);
                $cache_control_for_permitted_user->clearMatchingCacheKeys($cache_keys);
            }

            if (in_array((int) $this->payload['route_parameters']['resource_type_id'], $public_resource_types, true)) {
                $cache_control->clearMatchingPublicCacheKeys($cache_keys);
            }
        } else {
            $cache_control->clearMatchingCacheKeys($cache_keys);
        }
    }

    public function failed(Throwable $exception)
    {
        Notification::route('mail', Config::get('api.app.config.admin_email'))
            ->notify(new FailedJob([
                    'message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'trace' => $exception->getTraceAsString()
                ])
            );
    }
}
