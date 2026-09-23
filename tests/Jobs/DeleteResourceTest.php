<?php

namespace Tests\Jobs;

use App\HttpRequest\Hash;
use App\Jobs\DeleteResource;
use App\Notifications\FailedJob;
use App\Notifications\ResourceDeleted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class DeleteResourceTest extends TestCase
{
    /** @test */
    public function soleOwnerCascadeDeletesResourceAndAllData(): void
    {
        Notification::fake();

        $hash = new Hash();

        $owner = $this->createUser();
        $this->actingAs($owner);

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);

        $raw_resource_type_id = $hash->decode('resource-type', $resource_type_id);
        $raw_resource_id = $hash->decode('resource', $resource_id);
        $raw_item_id = $hash->decode('item', $item_id);

        $job = new DeleteResource($owner->id, $raw_resource_type_id, $raw_resource_id);
        $job->handle();

        $this->assertDatabaseMissing('resource', ['id' => $raw_resource_id]);
        $this->assertDatabaseMissing('item', ['id' => $raw_item_id]);
        $this->assertDatabaseMissing('item_type_allocated_expense', ['item_id' => $raw_item_id]);
        $this->assertDatabaseHas('resource_type', ['id' => $raw_resource_type_id]);

        Notification::assertSentOnDemand(ResourceDeleted::class);
        Notification::assertSentOnDemandTimes(FailedJob::class, 0);
    }

    /** @test */
    public function additionalPermittedUserOnlyRemovesThatUsersPermission(): void
    {
        Notification::fake();

        $owner = $this->createUser();
        $this->actingAs($owner);

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $other_user = $this->createUser();
        $this->postToPermittedUserCreate($resource_type_id, ['email' => $other_user->email])
            ->assertStatus(204);

        $hash = new Hash();
        $raw_resource_type_id = $hash->decode('resource-type', $resource_type_id);
        $raw_resource_id = $hash->decode('resource', $resource_id);

        $job = new DeleteResource($other_user->id, $raw_resource_type_id, $raw_resource_id);
        $job->handle();

        $this->assertDatabaseMissing('permitted_user', [
            'resource_type_id' => $raw_resource_type_id,
            'user_id' => $other_user->id,
        ]);
        $this->assertDatabaseHas('resource', ['id' => $raw_resource_id]);

        Notification::assertSentOnDemandTimes(ResourceDeleted::class, 0);
        Notification::assertSentOnDemandTimes(FailedJob::class, 0);
    }

    /** @test */
    public function transactionFailureDoesNotSendASuccessNotification(): void
    {
        Notification::fake();

        $hash = new Hash();

        $owner = $this->createUser();
        $this->actingAs($owner);

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $raw_resource_type_id = $hash->decode('resource-type', $resource_type_id);
        $raw_resource_id = $hash->decode('resource', $resource_id);

        // Grab a real connection before the facade is mocked below, so we can
        // still verify DB state without going through the mocked DB facade.
        $connection = DB::connection();

        DB::partialMock()
            ->shouldReceive('transaction')
            ->once()
            ->andThrow(new \Exception('Simulated transaction failure'));

        $job = new DeleteResource($owner->id, $raw_resource_type_id, $raw_resource_id);
        $job->handle();

        $this->assertTrue(
            $connection->table('resource')->where('id', $raw_resource_id)->exists(),
            'The resource should still exist after a simulated transaction failure'
        );

        Notification::assertSentOnDemandTimes(ResourceDeleted::class, 0);
    }
}
