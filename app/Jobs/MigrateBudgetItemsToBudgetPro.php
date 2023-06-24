<?php

namespace App\Jobs;

use App\Models\Resource;
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
            $this->fail(new \Exception('No budget resource type found for user id:' . $this->user_id));
        }

        if (count($resource_type) > 1) {
            $this->fail(new \Exception('There appears to be more than one free budget resource type, something has going wrong somewhere for user id:' . $this->user_id));
        }

        $budget_resource_type_id = $resource_type[0]->id;

        // Check to see if there is a budget-pro resource type
        $resource_type = $this->fetchResourceType($this->user_id, 'budget-pro');

        if (count($resource_type) === 0) {
            $this->fail(new \Exception('No budget-pro resource type found for user id:' . $this->user_id));
        }

        if (count($resource_type) > 1) {
            $this->fail(new \Exception('There appears to be more than one budget-pro resource type, something has going wrong somewhere for user id:' . $this->user_id));
        }

        $budget_pro_resource_type_id = $resource_type[0]->id;


        // Check to see if there is a budget resource
        $resource = $this->fetchResource($budget_resource_type_id);

        if (count($resource) === 0) {
            $this->fail(new \Exception('No budget resources for user id: ' . $this->user_id));
        }

        if (count($resource) > 1) {
            $this->fail(new \Exception('More than one budget resources for user id: ' . $this->user_id));
        }

        $budget_resource_id = $resource[0]->id;
        $budget_resource_data = $resource[0]->data;

        // Check to see if there is a budget-pro resource
        $resource = $this->fetchResource($budget_pro_resource_type_id);

        if (count($resource) === 0) {
            $this->fail(new \Exception('No budget-pro resources for user id: ' . $this->user_id));
        }

        if (count($resource) > 1) {
            $this->fail(new \Exception('More than one budget-pro resources for user id: ' . $this->user_id));
        }

        $budget_pro_resource_id = $resource[0]->id;

        // Check to see how many budget items there are
        $budget_items = $this->fetchBudgetItems($budget_resource_id);

        if (count($budget_items) === 0) {
            $this->fail(new \Exception('No budget items, nothing to migrate for user id: ' . $this->user_id));
        }

        // Check to see how many budget pro items there are
        $budget_pro_items = $this->fetchBudgetProItems($budget_pro_resource_id);

        if (count($budget_pro_items) !== 0) {
            $this->fail(new \Exception('There are items in the budget pro budget, can\'t process the migration'));
        }

        try {
            DB::transaction(function() use (
                $budget_items,
                $budget_pro_resource_id,
                $budget_pro_resource_type_id,
                $budget_resource_data
            ) {
                foreach ($budget_items as $item) {
                    $item_model = new \App\Models\Item();
                    $item_model->resource_id = $budget_pro_resource_id;
                    $item_model->created_by = $this->user_id;
                    $item_model->save();

                    $budget_pro_model = new \App\ItemType\BudgetPro\Models\Item();
                    $budget_pro_model->item_id = $item_model->id;
                    $budget_pro_model->name = $item->name;
                    $budget_pro_model->account = $item->account;
                    $budget_pro_model->target_account = $item->target_account;
                    $budget_pro_model->description = $item->description;
                    $budget_pro_model->amount = $item->amount;
                    $budget_pro_model->currency_id = $item->currency_id;
                    $budget_pro_model->category = $item->category;
                    $budget_pro_model->start_date = $item->start_date;
                    $budget_pro_model->end_date = $item->end_date;
                    $budget_pro_model->disabled = $item->disabled;
                    $budget_pro_model->frequency = $item->frequency;
                    $budget_pro_model->created_at = $item_model->created_at;
                    $budget_pro_model->save();
                }

                $resource = (new Resource())->instance($budget_pro_resource_type_id, $budget_pro_resource_id);
                if ($resource !== null) {
                    $resource->data = $budget_resource_data;
                    $resource->save();
                }
            });
        } catch (\Exception) {
            $this->fail(new \Exception('Failed to create all the items or save the resource data'));
        }

        $cache_clear_payload = (new \App\Cache\JobPayload())
            ->setGroupKey(\App\Cache\KeyGroup::RESOURCE_DELETE)
            ->setRouteParameters([
                'resource_type_id' => $budget_pro_resource_type_id,
                'resource_id' => $budget_pro_resource_id
            ])
            ->setUserId($this->user_id);

        ClearCache::dispatchSync($cache_clear_payload->payload());
    }

    public function fetchBudgetItems(int $resource_id): array
    {
        return DB::select('
            SELECT 
                `item_type_budget`.`name`,
                `item_type_budget`.`account`,
                `item_type_budget`.`target_account`,
                `item_type_budget`.`description`,
                `item_type_budget`.`amount`,
                `item_type_budget`.`currency_id`,
                `item_type_budget`.`category`,
                `item_type_budget`.`start_date`,
                `item_type_budget`.`end_date`,
                `item_type_budget`.`disabled`,
                `item_type_budget`.`frequency`
            FROM 
                `item` 
            INNER JOIN 
                `item_type_budget` ON 
                    `item`.`id` = `item_type_budget`.`item_id`
            WHERE
                `item`.`resource_id` = ?
        ', [$resource_id]);
    }

    public function fetchBudgetProItems(int $resource_id): array
    {
        return DB::select('
            SELECT 
                `item_type_budget_pro`.`name`,
                `item_type_budget_pro`.`account`,
                `item_type_budget_pro`.`target_account`,
                `item_type_budget_pro`.`description`,
                `item_type_budget_pro`.`amount`,
                `item_type_budget_pro`.`currency_id`,
                `item_type_budget_pro`.`category`,
                `item_type_budget_pro`.`start_date`,
                `item_type_budget_pro`.`end_date`,
                `item_type_budget_pro`.`disabled`,
                `item_type_budget_pro`.`frequency`
            FROM 
                `item` 
            INNER JOIN 
                `item_type_budget_pro` ON 
                    `item`.`id` = `item_type_budget_pro`.`item_id`
            WHERE
                `item`.`resource_id` = ?
        ', [$resource_id]);
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

    public function failed(Throwable $exception): void
    {
        Notification::route('mail', Config::get('api.app.config.admin_email'))
            ->notify(new FailedJob([
                    'message' => 'Budget Migration Failed: ' . $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'trace' => $exception->getTraceAsString()
                ])
            );
    }
}
