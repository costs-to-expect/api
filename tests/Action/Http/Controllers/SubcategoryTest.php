<?php

namespace Tests\Action\Http\Controllers;

use App\User;
use Tests\TestCase;

final class SubcategoryTest extends TestCase
{
    /** @test */
    public function createAllocatedExpenseSubcategoryFailsNoDescriptionInPayload(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);

        $response = $this->createSubcategory(
            $resource_type_id,
            $category_id,
            [
                'name' => $this->faker->text(200),
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createAllocatedExpenseSubcategoryFailsNoPayload(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);

        $response = $this->createSubcategory(
            $resource_type_id,
            $category_id,
            []
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createAllocatedExpenseSubcategoryFailsNonUniqueName(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);
        $name = $this->faker->text();

        $response = $this->createSubcategory(
            $resource_type_id,
            $category_id,
            [
                'name' => $name,
                'description' => $this->faker->text()
            ]
        );

        $response->assertStatus(201);

        // Create another with the same name
        $response = $this->createSubcategory(
            $resource_type_id,
            $category_id,
            [
                'name' => $name,
                'description' => $this->faker->text()
            ]
        );

        $response->assertStatus(422);
    }

       /** @test */
    public function createAllocatedExpenseSubcategoryForbiddenWhenCategoryIdInvalid(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();

        $response = $this->createSubcategory(
            $resource_type_id,
            'wwwwwwwwww',
            [
                'name' => $this->faker->text(200),
            ]
        );

        $response->assertStatus(403);
    }

    /** @test */
    public function createAllocatedExpenseSubcategorySuccess(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);

        $response = $this->createSubcategory(
            $resource_type_id,
            $category_id,
            [
                'name' => $this->faker->text(),
                'description' => $this->faker->text()
            ]
        );

        $response->assertStatus(201);
    }

    /** @test */
    public function createBudgetSubcategorySuccess(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);

        $response = $this->createSubcategory(
            $resource_type_id,
            $category_id,
            [
                'name' => $this->faker->text(),
                'description' => $this->faker->text()
            ]
        );

        $response->assertStatus(201);
    }

    /** @test */
    public function createBudgetProSubcategorySuccess(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetProResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);

        $response = $this->createSubcategory(
            $resource_type_id,
            $category_id,
            [
                'name' => $this->faker->text(),
                'description' => $this->faker->text()
            ]
        );

        $response->assertStatus(201);
    }

    /** @test */
    public function createGameSubcategorySuccess(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createGameResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);

        $response = $this->createSubcategory(
            $resource_type_id,
            $category_id,
            [
                'name' => $this->faker->text(),
                'description' => $this->faker->text()
            ]
        );

        $response->assertStatus(201);
    }

    /** @test */
    public function deleteAllocatedExpenseSubcategorySuccess(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);
        $subcategory_id = $this->createRandomSubcategory($resource_type_id, $category_id);

        $response = $this->deleteSubcategory(
            $resource_type_id,
            $category_id,
            $subcategory_id
        );

        $response->assertStatus(204);
    }

    /** @test */
    public function deleteBudgetSubcategorySuccess(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);
        $subcategory_id = $this->createRandomSubcategory($resource_type_id, $category_id);

        $response = $this->deleteSubcategory(
            $resource_type_id,
            $category_id,
            $subcategory_id
        );

        $response->assertStatus(204);
    }

    /** @test */
    public function deleteBudgetProSubcategorySuccess(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetProResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);
        $subcategory_id = $this->createRandomSubcategory($resource_type_id, $category_id);

        $response = $this->deleteSubcategory(
            $resource_type_id,
            $category_id,
            $subcategory_id
        );

        $response->assertStatus(204);
    }

    /** @test */
    public function deleteGameSubcategorySuccess(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createGameResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);
        $subcategory_id = $this->createRandomSubcategory($resource_type_id, $category_id);

        $response = $this->deleteSubcategory(
            $resource_type_id,
            $category_id,
            $subcategory_id
        );

        $response->assertStatus(204);
    }

    /** @test */
    public function updateAllocatedExpenseSubcategoryFailsExtraFieldsInPayload(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);
        $subcategory_id = $this->createRandomSubcategory($resource_type_id, $category_id);

        $response = $this->updateSubcategory(
            $resource_type_id,
            $category_id,
            $subcategory_id,
            [
                'extra_field' => $this->faker->text(100)
            ]
        );

        $response->assertStatus(400);
    }

    /** @test */
    public function updateAllocatedExpenseSubcategoryFailsNoPayload(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);
        $subcategory_id = $this->createRandomSubcategory($resource_type_id, $category_id);

        $response = $this->updateSubcategory(
            $resource_type_id,
            $category_id,
            $subcategory_id,
            []
        );

        $response->assertStatus(400);
    }

    /** @test */
    public function updateAllocatedExpenseSubcategoryFailsNonUniqueName(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);

        // Create first subcategory
        $name = $this->faker->text(200);

        $response = $this->createSubcategory(
            $resource_type_id,
            $category_id,
            [
                'name' => $name,
                'description' => $this->faker->text(200)
            ]
        );

        $response->assertStatus(201);

        // Create second subcategory
        $response = $this->createSubcategory(
            $resource_type_id,
            $category_id,
            [
                'name' => $this->faker->text(200),
                'description' => $this->faker->text(200)
            ]
        );

        $response->assertStatus(201);

        $subcategory_id = $response->json('id');

        // Attempt to set name of second subcategory to first name
        $response = $this->updateSubcategory(
            $resource_type_id,
            $category_id,
            $subcategory_id,
            [
                'name' => $name
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function updateAllocatedExpenseSubcategoryDescriptionSuccess(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);
        $subcategory_id = $this->createRandomSubcategory($resource_type_id, $category_id);

        $response = $this->updateSubcategory(
            $resource_type_id,
            $category_id,
            $subcategory_id,
            [
                'description' => $this->faker->text(25)
            ]
        );

        $response->assertStatus(204);
    }

    /** @test */
    public function updateAllocatedExpenseSubcategoryNameSuccess(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);
        $subcategory_id = $this->createRandomSubcategory($resource_type_id, $category_id);

        $response = $this->updateSubcategory(
            $resource_type_id,
            $category_id,
            $subcategory_id,
            [
                'name' => $this->faker->text(25)
            ]
        );

        $response->assertStatus(204);
    }

    /** @test */
    public function updateAllocatedExpenseSubcategorySuccess(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);
        $subcategory_id = $this->createRandomSubcategory($resource_type_id, $category_id);

        $response = $this->updateSubcategory(
            $resource_type_id,
            $category_id,
            $subcategory_id,
            [
                'name' => $this->faker->text(25),
                'description' => $this->faker->text(25)
            ]
        );

        $response->assertStatus(204);
    }

    /** @test */
    public function updateBudgetSubcategorySuccess(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);
        $subcategory_id = $this->createRandomSubcategory($resource_type_id, $category_id);

        $response = $this->updateSubcategory(
            $resource_type_id,
            $category_id,
            $subcategory_id,
            [
                'name' => $this->faker->text(25),
                'description' => $this->faker->text(25)
            ]
        );

        $response->assertStatus(204);
    }

    /** @test */
    public function updateBudgetProSubcategorySuccess(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetProResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);
        $subcategory_id = $this->createRandomSubcategory($resource_type_id, $category_id);

        $response = $this->updateSubcategory(
            $resource_type_id,
            $category_id,
            $subcategory_id,
            [
                'name' => $this->faker->text(25),
                'description' => $this->faker->text(25)
            ]
        );

        $response->assertStatus(204);
    }

    /** @test */
    public function updateGameSubcategorySuccess(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createGameResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);
        $subcategory_id = $this->createRandomSubcategory($resource_type_id, $category_id);

        $response = $this->updateSubcategory(
            $resource_type_id,
            $category_id,
            $subcategory_id,
            [
                'name' => $this->faker->text(25),
                'description' => $this->faker->text(25)
            ]
        );

        $response->assertStatus(204);
    }
}
