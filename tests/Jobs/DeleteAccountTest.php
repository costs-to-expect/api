<?php

namespace Tests\Jobs;

use App\HttpRequest\Hash;
use App\Jobs\DeleteAccount;
use App\Notifications\FailedJob;
use App\Notifications\ResourceTypeDeleted;
use App\User;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class DeleteAccountTest extends TestCase
{
    /** @test */
    public function soleOwnerAccountCascadeDeletesEverythingAndTheUser(): void
    {
        Notification::fake();

        $hash = new Hash();

        $owner = $this->createUser();
        $this->actingAs($owner);

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);

        $raw_resource_type_id = $hash->decode('resource-type', $resource_type_id);
        $raw_resource_id = $hash->decode('resource', $resource_id);

        $job = new DeleteAccount($owner->id);
        $job->handle();

        $this->assertDatabaseMissing('resource_type', ['id' => $raw_resource_type_id]);
        $this->assertDatabaseMissing('resource', ['id' => $raw_resource_id]);
        $this->assertDatabaseMissing('users', ['id' => $owner->id]);

        Notification::assertSentOnDemand(ResourceTypeDeleted::class);
        Notification::assertSentOnDemandTimes(FailedJob::class, 0);
    }

    /** @test */
    public function additionalPermittedUserIsStrippedButUserIsStillDeleted(): void
    {
        Notification::fake();

        $owner = $this->createUser();
        $this->actingAs($owner);

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();

        $other_user = $this->createUser();
        $this->postToPermittedUserCreate($resource_type_id, ['email' => $other_user->email])
            ->assertStatus(204);

        $hash = new Hash();
        $raw_resource_type_id = $hash->decode('resource-type', $resource_type_id);

        $job = new DeleteAccount($other_user->id);
        $job->handle();

        $this->assertDatabaseMissing('permitted_user', [
            'resource_type_id' => $raw_resource_type_id,
            'user_id' => $other_user->id,
        ]);
        $this->assertDatabaseHas('resource_type', ['id' => $raw_resource_type_id]);
        $this->assertDatabaseMissing('users', ['id' => $other_user->id]);

        Notification::assertSentOnDemandTimes(ResourceTypeDeleted::class, 0);
        Notification::assertSentOnDemandTimes(FailedJob::class, 0);
    }

    /** @test */
    public function userWithNoResourceTypesIsJustDeleted(): void
    {
        Notification::fake();

        $user_id = $this->createUserAndReturnId();

        $job = new DeleteAccount($user_id);
        $job->handle();

        $this->assertDatabaseMissing('users', ['id' => $user_id]);

        Notification::assertSentOnDemandTimes(FailedJob::class, 0);
    }
}
