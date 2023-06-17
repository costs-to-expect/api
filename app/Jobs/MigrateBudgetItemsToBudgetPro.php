<?php

namespace App\Jobs;

use App\Notifications\FailedJob;
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

class MigrateBudgetItemsToBudgetPro implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected int $user_id;

    public function __construct(int $user_id)
    {
        $this->user_id = $user_id;
    }

    public function handle()
    {
        // Check to see if there is a budget resource type
        $resource_type = $this->fetchResourceType($this->user_id, 'budget');

        if (count($resource_type) === 0) {
            $this->fail(new \Exception('Budget Pro migration failed, no budget resource type found for user id:' . $this->user_id));
        }

        if (count($resource_type) > 1) {
            $this->fail(new \Exception('Budget Pro migration failed, there appears to be more than one free budget resource type, something has going wrong somewhere for user id:' . $this->user_id));
        }

        $budget_resource_type_id = $resource_type[0]['id'];

        // Check to see if there is a budget-pro resource type
        $resource_type = $this->fetchResourceType($this->user_id, 'budget-pro');

        if (count($resource_type) === 0) {
            $this->fail(new \Exception('Budget Pro migration failed, no budget-pro resource type found for user id:' . $this->user_id));
        }

        if (count($resource_type) > 1) {
            $this->fail(new \Exception('Budget Pro migration failed, there appears to be more than one budget-pro resource type, something has going wrong somewhere for user id:' . $this->user_id));
        }

        $budget_pro_resource_type_id = $resource_type[0]['id'];


        // Check to see if there is a budget resource
        $resource = $this->fetchResource($budget_resource_type_id);

        if (count($resource) === 0) {
            $this->fail(new \Exception('Budget Pro migration failed, no budget resources for user id: ' . $this->user_id));
        }

        if (count($resource) > 1) {
            $this->fail(new \Exception('Budget Pro migration failed, more than one budget resources for user id: ' . $this->user_id));
        }

        $budget_resource_id = $resource[0]['id'];
        $budget_resource_data = $resource[0]['data'];

        // Check to see if there is a budget-pro resource
        $resource = $this->fetchResource($budget_pro_resource_type_id);

        if (count($resource) === 0) {
            $this->fail(new \Exception('Budget Pro migration failed, no budget-pro resources for user id: ' . $this->user_id));
        }

        if (count($resource) > 1) {
            $this->fail(new \Exception('Budget Pro migration failed, more than one budget-pro resources for user id: ' . $this->user_id));
        }

        $budget_pro_resource_id = $resource[0]['id'];



        // Check to see if there are items
        // Check to see if there is a budget pro resource
        // Copy over the resource data
        // Copy over the items
    }

    private function fetchResource(int $resource_type_id): array
    {
        return DB::select('
            SELECT 
                `resource`.`id`, `resource`.`data`
            FROM `resource`
            WHERE `resource`.`resource_type_id` = ?
        ', [$resource_type_id]);
    }

    private function fetchResourceType(int $user_id, string $item_type): array
    {
       return DB::select('
            SELECT 
                `resource_type`.`id`
            FROM `resource_type` 
            INNER JOIN `permitted_user` ON 
                `resource_type`.`id` = `permitted_user`.`resource_type_id` AND 
                `permitted_user`.`user_id` = ? 
            INNER JOIN `resource_type_item_type` ON 
                `resource_type`.`id` = `resource_type_item_type`.`resource_type_id`
            INNER JOIN `item_type` ON 
                `resource_type_item_type`.`item_type_id` = `item_type`.`id`
            WHERE 
                `item_type`.`name` = ?;
        ', [$user_id, $item_type]);
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
