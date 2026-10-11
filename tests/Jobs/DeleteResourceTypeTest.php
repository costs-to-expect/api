<?php

namespace Tests\Jobs;

use App\HttpRequest\Hash;
use App\Jobs\DeleteResourceType;
use App\Notifications\FailedJob;
use App\Notifications\ResourceTypeDeleted;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DeleteResourceTypeTest extends TestCase
{
    #[Test]
    public function soleOwnerCascadeDeletesResourceTypeAndAllData(): void
    {
        Notification::fake();

        $hash = new Hash();

        $owner = $this->createUser();
        $this->actingAs($owner);

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);
        $category_id = $this->quickCreateRandomCategory($resource_type_id);
        $item_category_id = $this->quickCreateItemCategory($resource_type_id, $resource_id, $item_id, $category_id);
        $subcategory_id = $this->quickCreateRandomSubcategory($resource_type_id, $category_id);
        $this->quickCreateItemSubcategory($resource_type_id, $resource_id, $item_id, $item_category_id, $subcategory_id);

        $raw_resource_type_id = $hash->decode('resource-type', $resource_type_id);
        $raw_resource_id = $hash->decode('resource', $resource_id);

        $job = new DeleteResourceType($owner->id, $raw_resource_type_id);
        $job->handle();

        $this->assertDatabaseMissing('resource_type', ['id' => $raw_resource_type_id]);
        $this->assertDatabaseMissing('resource', ['id' => $raw_resource_id]);
        $this->assertDatabaseMissing('category', ['resource_type_id' => $raw_resource_type_id]);
        $this->assertDatabaseMissing('sub_category', ['id' => $hash->decode('subcategory', $subcategory_id)]);
        $this->assertDatabaseMissing('resource_type_item_type', ['resource_type_id' => $raw_resource_type_id]);
        $this->assertDatabaseMissing('permitted_user', ['resource_type_id' => $raw_resource_type_id]);
        $this->assertDatabaseMissing('item', ['resource_id' => $raw_resource_id]);
        $this->assertDatabaseMissing('item_category', ['item_id' => $hash->decode('item', $item_id)]);

        Notification::assertSentOnDemand(ResourceTypeDeleted::class);
        Notification::assertSentOnDemandTimes(FailedJob::class, 0);
    }

    #[Test]
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

        $job = new DeleteResourceType($other_user->id, $raw_resource_type_id);
        $job->handle();

        $this->assertDatabaseMissing('permitted_user', [
            'resource_type_id' => $raw_resource_type_id,
            'user_id' => $other_user->id,
        ]);
        $this->assertDatabaseHas('resource_type', ['id' => $raw_resource_type_id]);
        $this->assertDatabaseHas('resource', ['id' => $raw_resource_id]);

        Notification::assertSentOnDemandTimes(ResourceTypeDeleted::class, 0);
        Notification::assertSentOnDemandTimes(FailedJob::class, 0);
    }

    #[Test]
    public function aPermissionIsFoundByTheUserNotByItsOwnId(): void
    {
        Notification::fake();

        $owner = $this->createUser();
        $this->actingAs($owner);

        // Another resource type first, so the permission rows no longer have the same ids as the users
        $this->quickCreateAllocatedExpenseResourceType();

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();

        $other_user = $this->createUser();
        $this->postToPermittedUserCreate($resource_type_id, ['email' => $other_user->email])
            ->assertStatus(204);

        $raw_resource_type_id = (new Hash())->decode('resource-type', $resource_type_id);

        $permission = \Illuminate\Support\Facades\DB::table('permitted_user')
            ->where('resource_type_id', $raw_resource_type_id)
            ->where('user_id', $other_user->id)
            ->first();
        $this->assertNotSame((int) $other_user->id, (int) $permission->id, 'The test needs the two to differ');

        (new DeleteResourceType($other_user->id, $raw_resource_type_id))->handle();

        $this->assertDatabaseMissing('permitted_user', ['id' => $permission->id]);
        $this->assertDatabaseHas('resource_type', ['id' => $raw_resource_type_id]);

        Notification::assertSentOnDemandTimes(ResourceTypeDeleted::class, 0);
    }

    #[Test]
    public function aUserWithNoPermissionOnASharedResourceTypeFailsTheJobAndChangesNothing(): void
    {
        Notification::fake();

        $owner = $this->createUser();
        $this->actingAs($owner);

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();

        $other_user = $this->createUser();
        $this->postToPermittedUserCreate($resource_type_id, ['email' => $other_user->email])
            ->assertStatus(204);

        $stranger = $this->createUser();
        $raw_resource_type_id = (new Hash())->decode('resource-type', $resource_type_id);

        try {
            (new DeleteResourceType($stranger->id, $raw_resource_type_id))->handle();
            $this->fail('The job should not report success, nothing was deleted');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('no permission', $e->getMessage());
        }

        $this->assertDatabaseHas('resource_type', ['id' => $raw_resource_type_id]);
        $this->assertDatabaseHas('permitted_user', ['resource_type_id' => $raw_resource_type_id, 'user_id' => $owner->id]);
        $this->assertDatabaseHas('permitted_user', ['resource_type_id' => $raw_resource_type_id, 'user_id' => $other_user->id]);
    }
}
