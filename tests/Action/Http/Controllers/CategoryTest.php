<?php

namespace Tests\Action\Http\Controllers;

use App\HttpRequest\Hash;
use App\Models\ResourceType;
use App\User;
use Tests\TestCase;

final class CategoryTest extends TestCase
{
    /** @test */
    public function createAllocatedExpenseCategoryFailsNoDescriptionInPayload(): void
    {
        $this->actingAs(User::find(1));

        $id = $this->createAllocatedExpenseResourceType();

        $response = $this->createCategory(
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
        $this->actingAs(User::find(1));

        $id = $this->createAllocatedExpenseResourceType();

        $response = $this->createCategory(
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
        $this->actingAs(User::find($this->fetchRandomUser()->id)); // Random user

        $resource_type = ResourceType::query()
            ->join('permitted_user', 'resource_type.id', '=', 'permitted_user.resource_type_id')
            ->where('permitted_user.user_id', '=', 1)
            ->first();

        if ($resource_type !== null) {

            $resource_type_id = (new Hash())->encode('resource-type', $resource_type->id);

            $response = $this->createCategory(
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
        $this->actingAs(User::find(1));

        $id = $this->createAllocatedExpenseResourceType();

        $response = $this->createCategory(
            $id,
            []
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createAllocatedExpenseCategoryFailsNonUniqueName(): void
    {
        $this->actingAs(User::find(1));

        $id = $this->createAllocatedExpenseResourceType();

        $name = $this->faker->text(200);

        $response = $this->createCategory(
            $id,
            [
                'name' => $name,
                'description' => $this->faker->text(200),
            ]
        );

        $response->assertStatus(201);

        // Create again with non-unique name for resource type
        $response = $this->createResource(
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
        $this->actingAs(User::find(1));

        $id = $this->createAllocatedExpenseResourceType();

        $response = $this->createCategory(
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
        $this->actingAs(User::find(1));

        $id = $this->createBudgetResourceType();

        $response = $this->createCategory(
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
        $this->actingAs(User::find(1));

        $id = $this->createBudgetProResourceType();

        $response = $this->createCategory(
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
        $this->actingAs(User::find(1));

        $id = $this->createGameResourceType();

        $response = $this->createCategory(
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
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $id = $this->createRandomCategory($resource_type_id);

        $response = $this->deleteRequestedCategory($resource_type_id, 'ABCDEDFGFG');
        $response->assertStatus(403);

        $response = $this->deleteRequestedCategory($resource_type_id, $id);
        $response->assertStatus(204);
    }

    /** @test */
    public function deleteAllocatedExpenseCategorySuccess(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $id = $this->createRandomCategory($resource_type_id);

        $response = $this->deleteRequestedCategory($resource_type_id, $id);

        $response->assertStatus(204);
    }

    /** @test */
    public function deleteBudgetCategorySuccess(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetResourceType();
        $id = $this->createRandomCategory($resource_type_id);

        $response = $this->deleteRequestedCategory($resource_type_id, $id);

        $response->assertStatus(204);
    }

    /** @test */
    public function deleteBudgetProCategorySuccess(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetProResourceType();
        $id = $this->createRandomCategory($resource_type_id);

        $response = $this->deleteRequestedCategory($resource_type_id, $id);

        $response->assertStatus(204);
    }

    /** @test */
    public function deleteGameCategorySuccess(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createGameResourceType();
        $id = $this->createRandomCategory($resource_type_id);

        $response = $this->deleteRequestedCategory($resource_type_id, $id);

        $response->assertStatus(204);
    }

    /** @test */
    public function updateAllocatedExpenseCategoryFailsExtraFieldsInPayload(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);

        $response = $this->updateRequestedCategory(
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
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);

        $response = $this->updateRequestedCategory(
            $resource_type_id,
            $category_id,
            []
        );

        $response->assertStatus(400);
    }

    /** @test */
    public function updateAllocatedExpenseCategoryFailsNonUniqueName(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();

        // Create first category
        $name = $this->faker->text(200);
        $response = $this->createCategory(
            $resource_type_id,
            [
                'name' => $name,
                'description' => $this->faker->text(200)
            ]
        );

        $response->assertStatus(201);

        // Create second category
        $response = $this->createCategory(
            $resource_type_id,
            [
                'name' => $this->faker->text(200),
                'description' => $this->faker->text(200)
            ]
        );

        $response->assertStatus(201);
        $category_id = $response->json('id');

        // Attempt to set name of second category to first name
        $response = $this->updateRequestedCategory(
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
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);

        $response = $this->updateRequestedCategory(
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
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);

        $response = $this->updateRequestedCategory(
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
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetProResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);

        $response = $this->updateRequestedCategory(
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
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createGameResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);

        $response = $this->updateRequestedCategory(
            $resource_type_id,
            $category_id,
            [
                'name' => $this->faker->text(25)
            ]
        );

        $response->assertStatus(204);
    }
}
