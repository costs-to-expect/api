<?php

namespace Tests\Action\Http\Controllers;

use App\HttpRequest\Hash;
use App\Models\ResourceType;
use Tests\TestCase;

final class CategoryTest extends TestCase
{
    /** @test */
    public function createAllocatedExpenseCategoryFailsNoDescriptionInPayload(): void
    {
        $this->actingAs($this->createUser());

        $id = $this->quickCreateAllocatedExpenseResourceType();

        $response = $this->postToCategoryCreate(
            $id,
            [
                'name' => $this->faker->text(200),
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createAllocatedExpenseCategoryFailsNoNameInPayload(): void
    {
        $this->actingAs($this->createUser());

        $id = $this->quickCreateAllocatedExpenseResourceType();

        $response = $this->postToCategoryCreate(
            $id,
            [
                'description' => $this->faker->text(200),
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createAllocatedExpenseCategoryFailsNoPermissionToResourceType(): void
    {
        $user = $this->createUser();
        
        $this->actingAs($user);

        $resource_type = ResourceType::query()
            ->join('permitted_user', 'resource_type.id', '=', 'permitted_user.resource_type_id')
            ->where('permitted_user.user_id', '=', 1)
            ->first();

        if ($resource_type !== null) {

            $resource_type_id = (new Hash())->encode('resource-type', $resource_type->id);

            $response = $this->postToCategoryCreate(
                $resource_type_id,
                [
                    'name' => $this->faker->text(200),
                    'description' => $this->faker->text(200),
                ]
            );

            $response->assertStatus(403);

        } else {
            $this->fail('Unable to fetch a resource type for testing in');
        }
    }

    /** @test */
    public function createAllocatedExpenseCategoryFailsNoPayload(): void
    {
        $this->actingAs($this->createUser());

        $id = $this->quickCreateAllocatedExpenseResourceType();

        $response = $this->postToCategoryCreate(
            $id,
            []
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createAllocatedExpenseCategoryFailsNonUniqueName(): void
    {
        $this->actingAs($this->createUser());

        $id = $this->quickCreateAllocatedExpenseResourceType();

        $name = $this->faker->text(200);

        $response = $this->postToCategoryCreate(
            $id,
            [
                'name' => $name,
                'description' => $this->faker->text(200),
            ]
        );

        $response->assertStatus(201);

        // Create again with non-unique name for the resource type
        $response = $this->postToResourceCreate(
            $id,
            [
                'name' => $name,
                'description' => $this->faker->text(200),
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createAllocatedExpenseCategorySuccess(): void
    {
        $this->actingAs($this->createUser());

        $id = $this->quickCreateAllocatedExpenseResourceType();

        $response = $this->postToCategoryCreate(
            $id,
            [
                'name' => $this->faker->text(200),
                'description' => $this->faker->text(200),
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonMatchesCategorySchema($response->content());
    }

    /** @test */
    public function createBudgetCategorySuccess(): void
    {
        $this->actingAs($this->createUser());

        $id = $this->quickCreateBudgetResourceType();

        $response = $this->postToCategoryCreate(
            $id,
            [
                'name' => $this->faker->text(200),
                'description' => $this->faker->text(200),
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonMatchesCategorySchema($response->content());
    }

    /** @test */
    public function createBudgetProCategorySuccess(): void
    {
        $this->actingAs($this->createUser());

        $id = $this->quickCreateBudgetProResourceType();

        $response = $this->postToCategoryCreate(
            $id,
            [
                'name' => $this->faker->text(200),
                'description' => $this->faker->text(200),
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonMatchesCategorySchema($response->content());
    }

    /** @test */
    public function createGameCategorySuccess(): void
    {
        $this->actingAs($this->createUser());

        $id = $this->quickCreateGameResourceType();

        $response = $this->postToCategoryCreate(
            $id,
            [
                'name' => $this->faker->text(200),
                'description' => $this->faker->text(200),
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonMatchesCategorySchema($response->content());
    }

    /** @test */
    public function deleteAllocatedExpenseCategoryFailsIdInvalid(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $id = $this->quickCreateRandomCategory($resource_type_id);

        $response = $this->deleteToCategoryDelete($resource_type_id, 'ABCDEDFGFG');
        $response->assertStatus(403);

        $response = $this->deleteToCategoryDelete($resource_type_id, $id);
        $response->assertStatus(204);
    }

    /** @test */
    public function deleteAllocatedExpenseCategorySuccess(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $id = $this->quickCreateRandomCategory($resource_type_id);

        $response = $this->deleteToCategoryDelete($resource_type_id, $id);

        $response->assertStatus(204);
    }

    /** @test */
    public function deleteBudgetCategorySuccess(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateBudgetResourceType();
        $id = $this->quickCreateRandomCategory($resource_type_id);

        $response = $this->deleteToCategoryDelete($resource_type_id, $id);

        $response->assertStatus(204);
    }

    /** @test */
    public function deleteBudgetProCategorySuccess(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateBudgetProResourceType();
        $id = $this->quickCreateRandomCategory($resource_type_id);

        $response = $this->deleteToCategoryDelete($resource_type_id, $id);

        $response->assertStatus(204);
    }

    /** @test */
    public function deleteGameCategorySuccess(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $id = $this->quickCreateRandomCategory($resource_type_id);

        $response = $this->deleteToCategoryDelete($resource_type_id, $id);

        $response->assertStatus(204);
    }

    /** @test */
    public function updateAllocatedExpenseCategoryFailsExtraFieldsInPayload(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $category_id = $this->quickCreateRandomCategory($resource_type_id);

        $response = $this->patchToCategoryUpdate(
            $resource_type_id,
            $category_id,
            [
                'extra' => $this->faker->text(100)
            ]
        );

        $response->assertStatus(400);
    }

    /** @test */
    public function updateAllocatedExpenseCategoryFailsNoPayload(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $category_id = $this->quickCreateRandomCategory($resource_type_id);

        $response = $this->patchToCategoryUpdate(
            $resource_type_id,
            $category_id,
            []
        );

        $response->assertStatus(400);
    }

    /** @test */
    public function updateAllocatedExpenseCategoryFailsNonUniqueName(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();

        // Create first category
        $name = $this->faker->text(200);
        $response = $this->postToCategoryCreate(
            $resource_type_id,
            [
                'name' => $name,
                'description' => $this->faker->text(200)
            ]
        );

        $response->assertStatus(201);

        // Create second category
        $response = $this->postToCategoryCreate(
            $resource_type_id,
            [
                'name' => $this->faker->text(200),
                'description' => $this->faker->text(200)
            ]
        );

        $response->assertStatus(201);
        $category_id = $response->json('id');

        // Attempt to set name of second category to first name
        $response = $this->patchToCategoryUpdate(
            $resource_type_id,
            $category_id,
            [
                'name' => $name
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function updateAllocatedExpenseCategorySuccess(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $category_id = $this->quickCreateRandomCategory($resource_type_id);

        $response = $this->patchToCategoryUpdate(
            $resource_type_id,
            $category_id,
            [
                'name' => $this->faker->text(25)
            ]
        );

        $response->assertStatus(204);
    }

    /** @test */
    public function updateBudgetCategorySuccess(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateBudgetResourceType();
        $category_id = $this->quickCreateRandomCategory($resource_type_id);

        $response = $this->patchToCategoryUpdate(
            $resource_type_id,
            $category_id,
            [
                'name' => $this->faker->text(25)
            ]
        );

        $response->assertStatus(204);
    }

    /** @test */
    public function updateBudgetProCategorySuccess(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateBudgetProResourceType();
        $category_id = $this->quickCreateRandomCategory($resource_type_id);

        $response = $this->patchToCategoryUpdate(
            $resource_type_id,
            $category_id,
            [
                'name' => $this->faker->text(25)
            ]
        );

        $response->assertStatus(204);
    }

    /** @test */
    public function updateGameCategorySuccess(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $category_id = $this->quickCreateRandomCategory($resource_type_id);

        $response = $this->patchToCategoryUpdate(
            $resource_type_id,
            $category_id,
            [
                'name' => $this->faker->text(25)
            ]
        );

        $response->assertStatus(204);
    }
}
