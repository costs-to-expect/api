<?php

namespace App\Jobs;

use App\Models\Permission;
use App\Models\PermittedUser;
use App\Models\Utility;
use App\Notifications\FailedJob;
use App\Notifications\ResourceDeleted;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Throwable;

class DeleteResource implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected int $user_id;
    protected int $resource_type_id;
    protected int $resource_id;

    protected array $deletes;

    public int $tries = 1;

    public function __construct(
        int $user_id,
        int $resource_type_id,
        int $resource_id
    )
    {
        $this->user_id = $user_id;
        $this->resource_type_id = $resource_type_id;
        $this->resource_id = $resource_id;

        $this->deletes = [];
    }

    public function handle()
    {
        /*TODO This is confusing to read as you are actually fetching number of permitted users excluding the given user*/
        $permitted_users = (new Permission())->permittedUsersForResourceType($this->resource_type_id, $this->user_id);

        if (count($permitted_users) > 0) {
            $permitted_user = (new PermittedUser())->instance($this->resource_type_id, $this->user_id);
            $permitted_user?->delete();
        }

        if (count($permitted_users) === 0) {
            try {
                DB::transaction(function () {
                    $this->deletes['data'] = Utility::deleteItemData($this->resource_id);
                    $this->deletes['logs'] = Utility::deleteItemLogs($this->resource_id);
                    $this->deletes['subcategories'] = Utility::deleteItemSubcategories($this->resource_id);
                    $this->deletes['categories'] = Utility::deleteItemCategories($this->resource_id);
                    $this->deletes['transfers'] = Utility::deleteTransfers($this->resource_id);
                    $this->deletes['partial-transfers'] = Utility::deletePartialTransfers($this->resource_id);
                    $this->deletes['item-type-data'] = Utility::deleteItemTypeData($this->resource_type_id, $this->resource_id);
                    $this->deletes['items'] = Utility::deleteItems($this->resource_id);
                    $this->deletes['resource-item-subtype'] = Utility::deleteResourceItemSubType($this->resource_id);
                    $this->deletes['resource'] = Utility::deleteResource($this->resource_id);
                });
            } catch (Throwable $e) {
                $this->fail($e);
            }

            Notification::route('mail', Config::get('api.app.config.admin_email'))
                ->notify(
                    new ResourceDeleted($this->deletes)
                );
        }

        $cache_clear_payload = (new \App\Cache\JobPayload())
            ->setGroupKey(\App\Cache\KeyGroup::RESOURCE_DELETE)
            ->setRouteParameters([
                'resource_type_id' => $this->resource_type_id,
                'resource_id' => $this->resource_id
            ])
            ->setUserId($this->user_id);

        ClearCache::dispatchSync($cache_clear_payload->payload());
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
