<?php

namespace Tests\Feature\Http\Controllers;

use App\User;
use Tests\TestCase;

final class SubcategoryManageTest extends TestCase
{
    /** @test */
    public function createSubcategoryFailsNoDescriptionInPayload(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createRandomResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);

        $response = $this->createRequestedSubcategory(
            $resource_type_id,
            $category_id,
            [
                'name' => $this->faker->text(200),
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createSubcategoryFailsNoPayload(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createRandomResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);

        $response = $this->createRequestedSubcategory(
            $resource_type_id,
            $category_id,
            []
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createSubcategoryFailsNonUniqueName(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createRandomResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);
        $name = $this->faker->text();

        $response = $this->createRequestedSubcategory(
            $resource_type_id,
            $category_id,
            [
                'name' => $name,
                'description' => $this->faker->text()
            ]
        );

        $response->assertStatus(201);

        // Create another with the same name
        $response = $this->createRequestedSubcategory(
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
    public function createSubcategoryForbiddenWhenCategoryIdInvalid(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createRandomResourceType();

        $response = $this->createRequestedSubcategory(
            $resource_type_id,
            'wwwwwwwwww',
            [
                'name' => $this->faker->text(200),
            ]
        );

        $response->assertStatus(403);
    }

    /** @test */
    public function createSubcategorySuccess(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createRandomResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);

        $response = $this->createRequestedSubcategory(
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
    public function deleteSubcategorySuccess(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createRandomResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);
        $subcategory_id = $this->createRandomSubcategory($resource_type_id, $category_id);

        $response = $this->deleteRequestedSubcategory(
            $resource_type_id,
            $category_id,
            $subcategory_id
        );

        $response->assertStatus(204);
    }

    /** @test */
    public function updateSubcategoryFailsExtraFieldsInPayload(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createRandomResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);
        $subcategory_id = $this->createRandomSubcategory($resource_type_id, $category_id);

        $response = $this->updatedRequestedSubcategory(
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
    public function updateSubcategoryFailsNoPayload(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createRandomResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);
        $subcategory_id = $this->createRandomSubcategory($resource_type_id, $category_id);

        $response = $this->updatedRequestedSubcategory(
            $resource_type_id,
            $category_id,
            $subcategory_id,
            []
        );

        $response->assertStatus(400);
    }

    /** @test */
    public function updateSubcategoryFailsNonUniqueName(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createRandomResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);

        // Create first subcategory
        $name = $this->faker->text(200);

        $response = $this->createRequestedSubcategory(
            $resource_type_id,
            $category_id,
            [
                'name' => $name,
                'description' => $this->faker->text(200)
            ]
        );

        $response->assertStatus(201);

        // Create second subcategory
        $response = $this->createRequestedSubcategory(
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
        $response = $this->updatedRequestedSubcategory(
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
    public function updateSubcategoryDescriptionSuccess(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createRandomResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);
        $subcategory_id = $this->createRandomSubcategory($resource_type_id, $category_id);

        $response = $this->updatedRequestedSubcategory(
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
    public function updateSubcategoryNameSuccess(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createRandomResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);
        $subcategory_id = $this->createRandomSubcategory($resource_type_id, $category_id);

        $response = $this->updatedRequestedSubcategory(
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
    public function updateSubcategorySuccess(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createRandomResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);
        $subcategory_id = $this->createRandomSubcategory($resource_type_id, $category_id);

        $response = $this->updatedRequestedSubcategory(
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
