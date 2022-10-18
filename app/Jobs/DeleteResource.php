<?php

namespace App\Jobs;

use App\Models\Permission;
use App\Notifications\FailedJob;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
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
    protected bool $force;

    public function __construct(
        int $user_id,
        int $resource_type_id,
        int $resource_id,
        bool $force
    )
    {
        $this->user_id = $user_id;
        $this->resource_type_id = $resource_type_id;
        $this->resource_id = $resource_id;
        $this->force = $force;
    }

    public function handle()
    {
        $process = false;

        $permitted_users = (new Permission())->permittedUsersForResourceType($this->resource_type_id, $this->user_id);
        if (count($permitted_users) === 0) {
            $process = true;

        }

        if ($this->force === true && count($permitted_users) > 0) {
            $process = true;
        }

        if ($process === false) {
            $this->fail(new \Exception('There are additional permitted users for the resource type and the force boolean is not set to true'));
        }

    }

    protected function getResourceType()
    {

    }

    protected function getResource()
    {

    }

    protected function deleteItemData()
    {

    }

    protected function deleteItemLogs()
    {

    }

    protected function deleteItemSubcategories()
    {

    }

    protected function deleteItemCategories()
    {

    }

    protected function deletePartialTransfers()
    {

    }

    protected function deleteTransfers()
    {
        
    }

    protected function deleteItems()
    {

    }

    protected function deleteResource()
    {

    }

    public function failed(Throwable $exception)
    {
        $user = User::query()->find(1);
        $user->notify(new FailedJob([
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ]));
    }
}
