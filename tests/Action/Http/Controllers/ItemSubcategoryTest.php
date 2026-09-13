<?php

namespace Tests\Action\Http\Controllers;

use App\HttpRequest\Hash;
use App\Models\ResourceType;
use Tests\TestCase;

final class ItemSubcategoryTest extends TestCase
{
    /** @test */
    public function createItemSubcategoryFailsNoPayload(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);
        $category_id = $this->quickCreateRandomCategory($resource_type_id);
        $item_category_id = $this->quickCreateItemCategory($resource_type_id, $resource_id, $item_id, $category_id);

        $response = $this->postToItemSubcategoryCreate($resource_type_id, $resource_id, $item_id, $item_category_id, []);

        $response->assertStatus(422);
    }

    /** @test */
    public function createItemSubcategoryFailsInvalidSubcategoryId(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);
        $category_id = $this->quickCreateRandomCategory($resource_type_id);
        $item_category_id = $this->quickCreateItemCategory($resource_type_id, $resource_id, $item_id, $category_id);

        $response = $this->postToItemSubcategoryCreate($resource_type_id, $resource_id, $item_id, $item_category_id, [
            'subcategory_id' => 'ABCDEDFGFG'
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function createItemSubcategoryFailsNoPermissionToResourceType(): void
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

        $response = $this->postToItemSubcategoryCreate($resource_type_id, 'ABCDEDFGFG', 'ABCDEDFGFG', 'ABCDEDFGFG', [
            'subcategory_id' => 'ABCDEDFGFG'
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function createItemSubcategorySuccess(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);
        $category_id = $this->quickCreateRandomCategory($resource_type_id);
        $item_category_id = $this->quickCreateItemCategory($resource_type_id, $resource_id, $item_id, $category_id);
        $subcategory_id = $this->quickCreateRandomSubcategory($resource_type_id, $category_id);

        $response = $this->postToItemSubcategoryCreate($resource_type_id, $resource_id, $item_id, $item_category_id, [
            'subcategory_id' => $subcategory_id
        ]);

        $response->assertStatus(201);
        $this->assertJsonMatchesItemSubcategorySchema($response->content());
    }

    /** @test */
    public function createItemSubcategoryFailsAssignmentLimitReached(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);
        $category_id = $this->quickCreateRandomCategory($resource_type_id);
        $item_category_id = $this->quickCreateItemCategory($resource_type_id, $resource_id, $item_id, $category_id);

        $first_subcategory_id = $this->quickCreateRandomSubcategory($resource_type_id, $category_id);
        $this->quickCreateItemSubcategory($resource_type_id, $resource_id, $item_id, $item_category_id, $first_subcategory_id);

        $second_subcategory_id = $this->quickCreateRandomSubcategory($resource_type_id, $category_id);
        $response = $this->postToItemSubcategoryCreate($resource_type_id, $resource_id, $item_id, $item_category_id, [
            'subcategory_id' => $second_subcategory_id
        ]);

        $response->assertStatus(400);
    }

    /** @test */
    public function deleteItemSubcategoryFailsIdInvalid(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);
        $category_id = $this->quickCreateRandomCategory($resource_type_id);
        $item_category_id = $this->quickCreateItemCategory($resource_type_id, $resource_id, $item_id, $category_id);

        $response = $this->deleteToItemSubcategoryDelete($resource_type_id, $resource_id, $item_id, $item_category_id, 'ABCDEDFGFG');
        $response->assertStatus(403);
    }

    /** @test */
    public function deleteItemSubcategorySuccess(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);
        $category_id = $this->quickCreateRandomCategory($resource_type_id);
        $item_category_id = $this->quickCreateItemCategory($resource_type_id, $resource_id, $item_id, $category_id);
        $subcategory_id = $this->quickCreateRandomSubcategory($resource_type_id, $category_id);

        $item_subcategory_id = $this->quickCreateItemSubcategory($resource_type_id, $resource_id, $item_id, $item_category_id, $subcategory_id);

        $response = $this->deleteToItemSubcategoryDelete($resource_type_id, $resource_id, $item_id, $item_category_id, $item_subcategory_id);
        $response->assertStatus(204);
    }
}
