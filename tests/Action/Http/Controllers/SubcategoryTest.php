<?php

namespace Tests\Action\Http\Controllers;

use Tests\TestCase;

final class SubcategoryTest extends TestCase
{
    /** @test */
    public function createAllocatedExpenseSubcategoryFailsNoDescriptionInPayload(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $category_id = $this->quickCreateRandomCategory($resource_type_id);

        $response = $this->postToSubcategoryCreate(
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
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $category_id = $this->quickCreateRandomCategory($resource_type_id);

        $response = $this->postToSubcategoryCreate(
            $resource_type_id,
            $category_id,
            []
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createAllocatedExpenseSubcategoryFailsNonUniqueName(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $category_id = $this->quickCreateRandomCategory($resource_type_id);
        $name = $this->faker->text();

        $response = $this->postToSubcategoryCreate(
            $resource_type_id,
            $category_id,
            [
                'name' => $name,
                'description' => $this->faker->text()
            ]
        );

        $response->assertStatus(201);

        // Create another with the same name
        $response = $this->postToSubcategoryCreate(
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
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();

        $response = $this->postToSubcategoryCreate(
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
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $category_id = $this->quickCreateRandomCategory($resource_type_id);

        $response = $this->postToSubcategoryCreate(
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
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateBudgetResourceType();
        $category_id = $this->quickCreateRandomCategory($resource_type_id);

        $response = $this->postToSubcategoryCreate(
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
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateBudgetProResourceType();
        $category_id = $this->quickCreateRandomCategory($resource_type_id);

        $response = $this->postToSubcategoryCreate(
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
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $category_id = $this->quickCreateRandomCategory($resource_type_id);

        $response = $this->postToSubcategoryCreate(
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
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $category_id = $this->quickCreateRandomCategory($resource_type_id);
        $subcategory_id = $this->quickCreateRandomSubcategory($resource_type_id, $category_id);

        $response = $this->deleteToSubcategoryDelete(
            $resource_type_id,
            $category_id,
            $subcategory_id
        );

        $response->assertStatus(204);
    }

    /** @test */
    public function deleteBudgetSubcategorySuccess(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateBudgetResourceType();
        $category_id = $this->quickCreateRandomCategory($resource_type_id);
        $subcategory_id = $this->quickCreateRandomSubcategory($resource_type_id, $category_id);

        $response = $this->deleteToSubcategoryDelete(
            $resource_type_id,
            $category_id,
            $subcategory_id
        );

        $response->assertStatus(204);
    }

    /** @test */
    public function deleteBudgetProSubcategorySuccess(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateBudgetProResourceType();
        $category_id = $this->quickCreateRandomCategory($resource_type_id);
        $subcategory_id = $this->quickCreateRandomSubcategory($resource_type_id, $category_id);

        $response = $this->deleteToSubcategoryDelete(
            $resource_type_id,
            $category_id,
            $subcategory_id
        );

        $response->assertStatus(204);
    }

    /** @test */
    public function deleteGameSubcategorySuccess(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $category_id = $this->quickCreateRandomCategory($resource_type_id);
        $subcategory_id = $this->quickCreateRandomSubcategory($resource_type_id, $category_id);

        $response = $this->deleteToSubcategoryDelete(
            $resource_type_id,
            $category_id,
            $subcategory_id
        );

        $response->assertStatus(204);
    }

    /** @test */
    public function updateAllocatedExpenseSubcategoryFailsExtraFieldsInPayload(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $category_id = $this->quickCreateRandomCategory($resource_type_id);
        $subcategory_id = $this->quickCreateRandomSubcategory($resource_type_id, $category_id);

        $response = $this->patchToSubcategoryUpdate(
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
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $category_id = $this->quickCreateRandomCategory($resource_type_id);
        $subcategory_id = $this->quickCreateRandomSubcategory($resource_type_id, $category_id);

        $response = $this->patchToSubcategoryUpdate(
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
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $category_id = $this->quickCreateRandomCategory($resource_type_id);

        // Create first subcategory
        $name = $this->faker->text(200);

        $response = $this->postToSubcategoryCreate(
            $resource_type_id,
            $category_id,
            [
                'name' => $name,
                'description' => $this->faker->text(200)
            ]
        );

        $response->assertStatus(201);

        // Create second subcategory
        $response = $this->postToSubcategoryCreate(
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
        $response = $this->patchToSubcategoryUpdate(
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
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $category_id = $this->quickCreateRandomCategory($resource_type_id);
        $subcategory_id = $this->quickCreateRandomSubcategory($resource_type_id, $category_id);

        $response = $this->patchToSubcategoryUpdate(
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
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $category_id = $this->quickCreateRandomCategory($resource_type_id);
        $subcategory_id = $this->quickCreateRandomSubcategory($resource_type_id, $category_id);

        $response = $this->patchToSubcategoryUpdate(
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
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $category_id = $this->quickCreateRandomCategory($resource_type_id);
        $subcategory_id = $this->quickCreateRandomSubcategory($resource_type_id, $category_id);

        $response = $this->patchToSubcategoryUpdate(
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
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateBudgetResourceType();
        $category_id = $this->quickCreateRandomCategory($resource_type_id);
        $subcategory_id = $this->quickCreateRandomSubcategory($resource_type_id, $category_id);

        $response = $this->patchToSubcategoryUpdate(
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
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateBudgetProResourceType();
        $category_id = $this->quickCreateRandomCategory($resource_type_id);
        $subcategory_id = $this->quickCreateRandomSubcategory($resource_type_id, $category_id);

        $response = $this->patchToSubcategoryUpdate(
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
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $category_id = $this->quickCreateRandomCategory($resource_type_id);
        $subcategory_id = $this->quickCreateRandomSubcategory($resource_type_id, $category_id);

        $response = $this->patchToSubcategoryUpdate(
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
