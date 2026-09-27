<?php

namespace Tests\Action\Http\Controllers;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class SubcategoryTest extends TestCase
{
    #[Test]
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

    #[Test]
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

    #[Test]
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

       #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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
