<?php

namespace App\Jobs;

use App\Models\Permission;
use App\Models\PermittedUser;
use App\Models\Resource;
use App\Models\Utility;
use App\Notifications\FailedJob;
use App\Notifications\ResourceDeleted;
use App\Notifications\ResourceTypeDeleted;
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

class DeleteResourceType implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected int $user_id;
    protected int $resource_type_id;

    protected array $deletes;

    public int $tries = 1;

    public function __construct(
        int $user_id,
        int $resource_type_id
    )
    {
        $this->user_id = $user_id;
        $this->resource_type_id = $resource_type_id;

        $this->deletes = [];
    }

    public function handle()
    {
        $permitted_users = (new Permission())->additionalPermittedUsers($this->resource_type_id, $this->user_id);

        if (count($permitted_users) > 0) {

            /** Note
             * Yes, this is a hack as we are rewriting history when we removed a user from a resource type.
             * Eventually we will solve this another, probably by using a placeholder user for each resource type
             * so we can show something line [Deleted User] in responses rather than the current incorrect
             * behaviour
             */

            // Remove references to the user in the `permitted_user` table
            DB::update('UPDATE `permitted_user` SET `added_by` = ? WHERE `added_by` = ?', [$permitted_users[0], $this->user_id]);

            // Remove references to the user in the `item` table
            DB::update('UPDATE `item` SET `created_by` = ? WHERE `created_by` = ?', [$permitted_users[0], $this->user_id]);
            DB::update('UPDATE `item` SET `updated_by` = ? WHERE `updated_by` = ?', [$permitted_users[0], $this->user_id]);

            $permitted_user = (new PermittedUser())->instance($this->resource_type_id, $this->user_id);
            $permitted_user?->delete();
        }

        if (count($permitted_users) === 0) {
            $resources = (new Resource())->paginatedCollection($this->resource_type_id);

            foreach ($resources as $resource) {
                $this->deleteResourceAndAllData($this->resource_type_id, $resource['resource_id']);
            }

            $this->deletes = [];

            try {
                DB::transaction(function () {
                    $this->deletes['subcategories'] = Utility::deleteSubcategories($this->resource_type_id);
                    $this->deletes['categories'] = Utility::deleteCategories($this->resource_type_id);
                    $this->deletes['resource-type-item-type'] = Utility::deleteResourceTypeItemType($this->resource_type_id);
                    $this->deletes['permitted-users'] = Utility::deletePermittedUsers($this->resource_type_id);
                    $this->deletes['resource-type'] = Utility::deleteResourceType($this->resource_type_id);
                });
            } catch (Throwable $e) {
                $this->fail($e);
            }

            Notification::route('mail', Config::get('api.app.config.admin_email'))
                ->notify(
                    new ResourceTypeDeleted($this->deletes)
                );
        }

        $cache_clear_payload = (new \App\Cache\JobPayload())
            ->setGroupKey(\App\Cache\KeyGroup::RESOURCE_TYPE_DELETE)
            ->setRouteParameters([
                'resource_type_id' => $this->resource_type_id
            ])
            ->setUserId($this->user_id);

        ClearCache::dispatchSync($cache_clear_payload->payload());
    }

    protected function deleteResourceAndAllData(int $resource_type_id, int $resource_id)
    {
        $this->deletes = [];

        try {
            DB::transaction(function () use ($resource_type_id, $resource_id) {

                $this->deletes['data'] = Utility::deleteItemData($resource_id);
                $this->deletes['logs'] = Utility::deleteItemLogs($resource_id);
                $this->deletes['subcategories'] = Utility::deleteSubcategories($resource_id);
                $this->deletes['categories'] = Utility::deleteItemCategories($resource_id);
                $this->deletes['transfers'] = Utility::deleteTransfers($resource_id);
                $this->deletes['partial-transfers'] = Utility::deletePartialTransfers($resource_id);
                $this->deletes['item-type-data'] = Utility::deleteItemTypeData($resource_type_id, $resource_id);
                $this->deletes['items'] = Utility::deleteItems($resource_id);
                $this->deletes['resource-item-subtype'] = Utility::deleteResourceItemSubType($resource_id);
                $this->deletes['resource'] = Utility::deleteResource($resource_id);

            });


        } catch (Throwable $e) {
            $this->fail($e);
        }

        Notification::route('mail', Config::get('api.app.config.admin_email'))
            ->notify(new ResourceDeleted($this->deletes)
            );
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
