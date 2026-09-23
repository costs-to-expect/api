<?php

namespace Tests\Jobs;

use App\Jobs\MigrateBudgetItemsToBudgetPro;
use App\Notifications\FailedJob;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class MigrateBudgetItemsToBudgetProTest extends TestCase
{
    /** @test */
    public function handleReturnsCleanlyWhenNoBudgetResourceTypeExists(): void
    {
        $user_id = $this->createUserAndReturnId();

        $job = new MigrateBudgetItemsToBudgetPro($user_id);
        $job->handle();

        $this->assertDatabaseMissing('item_type_budget_pro', []);
    }

    /** @test */
    public function handleReturnsCleanlyWhenNoBudgetProResourceTypeExists(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $resource_type_id = $this->quickCreateBudgetResourceType();
        $resource_id = $this->quickCreateBudgetResource($resource_type_id);
        $this->quickCreateBudgetItem($resource_type_id, $resource_id);

        $job = new MigrateBudgetItemsToBudgetPro($user->id);
        $job->handle();

        $this->assertDatabaseMissing('item_type_budget_pro', []);
    }

    /** @test */
    public function handleReturnsCleanlyWhenNoBudgetItemsExistToMigrate(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        // Both resource types are created before any resource, to avoid the
        // stale permitted-resource-types cache snapshot from the first
        // resource-type.create hit shadowing the second (see
        // cte-api-test-controller-memoization memory).
        $resource_type_id = $this->quickCreateBudgetResourceType();
        $budget_pro_resource_type_id = $this->quickCreateBudgetProResourceType();

        $this->quickCreateBudgetResource($resource_type_id);
        $this->quickCreateBudgetProResource($budget_pro_resource_type_id);

        $job = new MigrateBudgetItemsToBudgetPro($user->id);
        $job->handle();

        $this->assertDatabaseMissing('item_type_budget_pro', []);
    }

    /** @test */
    public function handleReturnsCleanlyWhenBudgetProResourceAlreadyHasItems(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $resource_type_id = $this->quickCreateBudgetResourceType();
        $budget_pro_resource_type_id = $this->quickCreateBudgetProResourceType();

        $resource_id = $this->quickCreateBudgetResource($resource_type_id);
        $budget_pro_resource_id = $this->quickCreateBudgetProResource($budget_pro_resource_type_id);

        $this->quickCreateBudgetItem($resource_type_id, $resource_id);
        $this->quickCreateBudgetProItem($budget_pro_resource_type_id, $budget_pro_resource_id);

        $job = new MigrateBudgetItemsToBudgetPro($user->id);
        $job->handle();

        // Still only the one pre-existing budget pro item, nothing duplicated
        $this->assertDatabaseCount('item_type_budget_pro', 1);
    }

    /** @test */
    public function handleMigratesBudgetItemsToTheEmptyBudgetProResource(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $resource_type_id = $this->quickCreateBudgetResourceType();
        $budget_pro_resource_type_id = $this->quickCreateBudgetProResourceType();

        $resource_id = $this->quickCreateBudgetResource($resource_type_id);
        $budget_pro_resource_id = $this->quickCreateBudgetProResource($budget_pro_resource_type_id);

        $this->quickCreateBudgetItem($resource_type_id, $resource_id, ['name' => 'migrate-me-one']);
        $this->quickCreateBudgetItem($resource_type_id, $resource_id, ['name' => 'migrate-me-two']);

        $job = new MigrateBudgetItemsToBudgetPro($user->id);
        $job->handle();

        $this->assertDatabaseCount('item_type_budget_pro', 2);
        $this->assertDatabaseHas('item_type_budget_pro', ['name' => 'migrate-me-one']);
        $this->assertDatabaseHas('item_type_budget_pro', ['name' => 'migrate-me-two']);
    }

    /** @test */
    public function failedSendsAFailedJobNotification(): void
    {
        Notification::fake();

        $job = new MigrateBudgetItemsToBudgetPro(999999);
        $job->failed(new \Exception('Something went wrong during the migration'));

        Notification::assertSentOnDemand(
            FailedJob::class,
            function (FailedJob $notification) {
                $mail = $notification->toMail(null);

                return str_contains(implode(' ', $mail->introLines), 'Something went wrong during the migration');
            }
        );
    }
}
