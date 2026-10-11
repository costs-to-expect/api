<?php

namespace Tests\Jobs;

use App\Jobs\MigrateBudgetItemsToBudgetPro;
use App\Notifications\FailedJob;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MigrateBudgetItemsToBudgetProTest extends TestCase
{
    #[Test]
    public function handleReturnsCleanlyWhenNoBudgetResourceTypeExists(): void
    {
        $user_id = $this->createUserAndReturnId();

        $job = new MigrateBudgetItemsToBudgetPro($user_id);
        $job->handle();

        $this->assertDatabaseMissing('item_type_budget_pro', []);
    }

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    /**
     * The migration used to be for a user with exactly one Budget Pro budget, now Budget Pro lets them have up to
     * three and says which one is to get the items
     */
    private function itemsIn(string $resource_id): int
    {
        return \Illuminate\Support\Facades\DB::table('item')
            ->where('resource_id', (new \App\HttpRequest\Hash())->decode('resource', $resource_id))
            ->count();
    }

    #[Test]
    public function handleMigratesIntoTheBudgetProBudgetAskedForWhenTheUserHasSeveral(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $resource_type_id = $this->quickCreateBudgetResourceType();
        $budget_pro_resource_type_id = $this->quickCreateBudgetProResourceType();

        $resource_id = $this->quickCreateBudgetResource($resource_type_id);
        $first = $this->quickCreateBudgetProResource($budget_pro_resource_type_id);
        $second = $this->quickCreateBudgetProResource($budget_pro_resource_type_id);

        $this->quickCreateBudgetItem($resource_type_id, $resource_id, ['name' => 'migrate-me-one']);
        $this->quickCreateBudgetItem($resource_type_id, $resource_id, ['name' => 'migrate-me-two']);

        $second_id = (new \App\HttpRequest\Hash())->decode('resource', $second);

        (new MigrateBudgetItemsToBudgetPro($user->id, $second_id))->handle();

        $this->assertDatabaseCount('item_type_budget_pro', 2);
        $this->assertSame(2, $this->itemsIn($second), 'The items went to the one asked for');
        $this->assertSame(0, $this->itemsIn($first), 'And not to the other');
    }

    #[Test]
    public function handleStillRefusesWhenTheUserHasSeveralAndNoneIsAskedFor(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $resource_type_id = $this->quickCreateBudgetResourceType();
        $budget_pro_resource_type_id = $this->quickCreateBudgetProResourceType();

        $resource_id = $this->quickCreateBudgetResource($resource_type_id);
        $this->quickCreateBudgetProResource($budget_pro_resource_type_id);
        $this->quickCreateBudgetProResource($budget_pro_resource_type_id);

        $this->quickCreateBudgetItem($resource_type_id, $resource_id);

        (new MigrateBudgetItemsToBudgetPro($user->id))->handle();

        $this->assertDatabaseCount('item_type_budget_pro', 0);
    }

    #[Test]
    public function handleNeverMigratesIntoAResourceThatIsNotOneOfTheirBudgetProBudgets(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $resource_type_id = $this->quickCreateBudgetResourceType();
        $budget_pro_resource_type_id = $this->quickCreateBudgetProResourceType();
        $resource_id = $this->quickCreateBudgetResource($resource_type_id);
        $mine = $this->quickCreateBudgetProResource($budget_pro_resource_type_id);
        $this->quickCreateBudgetItem($resource_type_id, $resource_id);

        $hash = new \App\HttpRequest\Hash();

        // Their Budget the items come from (not a Budget Pro budget) and one that is nobody's
        foreach ([$hash->decode('resource', $resource_id), 999999] as $not_theirs) {
            (new MigrateBudgetItemsToBudgetPro($user->id, $not_theirs))->handle();
        }

        $this->assertDatabaseCount('item_type_budget_pro', 0);
        $this->assertSame(0, $this->itemsIn($mine), 'They do not go to their only Budget Pro budget instead');
    }

    #[Test]
    public function handleDoesNotMigrateIntoAskedForBudgetThatAlreadyHasItems(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $resource_type_id = $this->quickCreateBudgetResourceType();
        $budget_pro_resource_type_id = $this->quickCreateBudgetProResourceType();

        $resource_id = $this->quickCreateBudgetResource($resource_type_id);
        $first = $this->quickCreateBudgetProResource($budget_pro_resource_type_id);
        $second = $this->quickCreateBudgetProResource($budget_pro_resource_type_id);

        $this->quickCreateBudgetItem($resource_type_id, $resource_id);
        $this->quickCreateBudgetProItem($budget_pro_resource_type_id, $second);

        $second_id = (new \App\HttpRequest\Hash())->decode('resource', $second);

        (new MigrateBudgetItemsToBudgetPro($user->id, $second_id))->handle();

        $this->assertDatabaseCount('item_type_budget_pro', 1);
        $this->assertSame(0, $this->itemsIn($first));
    }
}
