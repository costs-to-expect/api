<?php

namespace App\Jobs;

use App\Models\Permission;
use App\Models\PermittedUser;
use App\Models\Resource;
use App\Models\Utility;
use App\Notifications\FailedJob;
use App\Notifications\ResourceDeleted;
use App\Notifications\ResourceTypeDeleted;
use App\User;
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

class DeleteAccount implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected int $user_id;

    protected array $deletes;

    public int $tries = 1;

    public function __construct(int $user_id)
    {
        $this->user_id = $user_id;

        $this->deletes = [];
    }

    public function handle()
    {
        $resource_types = (new Permission())->permittedResourceTypesForUser($this->user_id);

        foreach ($resource_types as $resource_type_id) {
            $additional_permitted_users = (new Permission())->additionalPermittedUsers($resource_type_id, $this->user_id);
            if (count($additional_permitted_users) === 0) {
                $resources = (new Resource())->paginatedCollection($resource_type_id);

                foreach ($resources as $resource) {
                    $this->deleteResourceAndAllData($resource_type_id, $resource['resource_id']);
                }

                $this->deletes = [];

                try {
                    DB::transaction(function () use ($resource_type_id) {
                        $this->deletes['subcategories'] = Utility::deleteSubcategories($resource_type_id);
                        $this->deletes['categories'] = Utility::deleteCategories($resource_type_id);
                        $this->deletes['resource-type-item-type'] = Utility::deleteResourceTypeItemType($resource_type_id);
                        $this->deletes['permitted-users'] = Utility::deletePermittedUsers($resource_type_id);
                        $this->deletes['resource-type'] = Utility::deleteResourceType($resource_type_id);
                    });


                } catch (Throwable $e) {
                    throw new \Exception($e->getMessage());
                }

                Notification::route('mail', Config::get('api.app.config.admin_email'))
                    ->notify(new ResourceTypeDeleted($this->deletes));
            }

            if (count($additional_permitted_users) > 0) {

                /** Note
                 * Yes, this is a hack as we are rewriting history when we removed a user from a resource type.
                 * Eventually we will solve this another, probably by using a placeholder user for each resource type
                 * so we can show something line [Deleted User] in responses rather than the current incorrect
                 * behaviour
                 */

                // Remove references to the user in the `permitted_user` table
                DB::update('UPDATE `permitted_user` SET `added_by` = ? WHERE `added_by` = ?', [$additional_permitted_users[0], $this->user_id]);

                // Remove references to the user in the `item` table
                DB::update('UPDATE `item` SET `created_by` = ? WHERE `created_by` = ?', [$additional_permitted_users[0], $this->user_id]);
                DB::update('UPDATE `item` SET `updated_by` = ? WHERE `updated_by` = ?', [$additional_permitted_users[0], $this->user_id]);

                $permitted_user = (new PermittedUser())->instance($resource_type_id, $this->user_id);
                $permitted_user?->delete();
            }

            $cache_clear_payload = (new \App\Cache\JobPayload())
                ->setGroupKey(\App\Cache\KeyGroup::RESOURCE_TYPE_DELETE)
                ->setRouteParameters([
                    'resource_type_id' => $resource_type_id
                ])
                ->setUserId($this->user_id);

            ClearCache::dispatchSync($cache_clear_payload->payload());
        }

        $user = (new User())->instance($this->user_id);
        $user?->delete();
    }

    protected function deleteResourceAndAllData(int $resource_type_id, int $resource_id)
    {
        $this->deletes = [];

        try {
            DB::transaction(function () use ($resource_type_id, $resource_id) {
                $this->deletes['data'] = Utility::deleteItemData($resource_id);
                $this->deletes['logs'] = Utility::deleteItemLogs($resource_id);
                $this->deletes['subcategories'] = Utility::deleteItemSubcategories($resource_id);
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
