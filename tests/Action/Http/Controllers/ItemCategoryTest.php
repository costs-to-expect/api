<?php

namespace Tests\Action\Http\Controllers;

use App\HttpRequest\Hash;
use App\Models\ResourceType;
use Tests\TestCase;

final class ItemCategoryTest extends TestCase
{
    /** @test */
    public function createItemCategoryFailsNoPayload(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);

        $response = $this->postToItemCategoryCreate($resource_type_id, $resource_id, $item_id, []);

        $response->assertStatus(422);
    }

    /** @test */
    public function createItemCategoryFailsInvalidCategoryId(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);

        $response = $this->postToItemCategoryCreate($resource_type_id, $resource_id, $item_id, [
            'category_id' => 'ABCDEDFGFG'
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function createItemCategoryFailsNoPermissionToResourceType(): void
    {
        $this->actingAs($this->createUser());

        $resource_type = ResourceType::query()
            ->join('permitted_user', 'resource_type.id', '=', 'permitted_user.resource_type_id')
            ->where('permitted_user.user_id', '=', 1)
            ->first();

        if ($resource_type === null) {
            $this->fail('Unable to fetch a resource type for testing in');
        }

        $resource_type_id = (new Hash())->encode('resource-type', $resource_type->id);

        $response = $this->postToItemCategoryCreate($resource_type_id, 'ABCDEDFGFG', 'ABCDEDFGFG', [
            'category_id' => 'ABCDEDFGFG'
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function createItemCategorySuccess(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);
        $category_id = $this->quickCreateRandomCategory($resource_type_id);

        $response = $this->postToItemCategoryCreate($resource_type_id, $resource_id, $item_id, [
            'category_id' => $category_id
        ]);

        $response->assertStatus(201);
        $this->assertJsonMatchesItemCategorySchema($response->content());
    }

    /** @test */
    public function createItemCategoryFailsAssignmentLimitReached(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);
        $first_category_id = $this->quickCreateRandomCategory($resource_type_id);

        $this->quickCreateItemCategory($resource_type_id, $resource_id, $item_id, $first_category_id);

        $second_category_id = $this->quickCreateRandomCategory($resource_type_id);
        $response = $this->postToItemCategoryCreate($resource_type_id, $resource_id, $item_id, [
            'category_id' => $second_category_id
        ]);

        $response->assertStatus(400);
    }

    /** @test */
    public function deleteItemCategoryFailsIdInvalid(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);

        $response = $this->deleteToItemCategoryDelete($resource_type_id, $resource_id, $item_id, 'ABCDEDFGFG');
        $response->assertStatus(403);
    }

    /** @test */
    public function deleteItemCategorySuccess(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);
        $category_id = $this->quickCreateRandomCategory($resource_type_id);

        $item_category_id = $this->quickCreateItemCategory($resource_type_id, $resource_id, $item_id, $category_id);

        $response = $this->deleteToItemCategoryDelete($resource_type_id, $resource_id, $item_id, $item_category_id);
        $response->assertStatus(204);
    }
}
