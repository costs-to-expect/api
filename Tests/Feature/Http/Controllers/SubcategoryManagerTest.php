<?php

namespace Tests\Feature\Http\Controllers;

use App\User;
use Tests\TestCase;

class SubcategoryManagerTest extends TestCase
{
    /** @test */
    public function create_subcategory_fails_no_description_in_payload(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAndReturnResourceTypeId();
        $category_id = $this->createAndReturnCategoryId($resource_type_id);

        $response = $this->postSubcategory(
            $resource_type_id,
            $category_id,
            [
                'name' => $this->faker->text(200),
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function create_subcategory_fails_no_payload(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAndReturnResourceTypeId();
        $category_id = $this->createAndReturnCategoryId($resource_type_id);

        $response = $this->postSubcategory(
            $resource_type_id,
            $category_id,
            []
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function create_subcategory_fails_non_unique_name(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAndReturnResourceTypeId();
        $category_id = $this->createAndReturnCategoryId($resource_type_id);
        $name = $this->faker->text();

        $response = $this->postSubcategory(
            $resource_type_id,
            $category_id,
            [
                'name' => $name,
                'description' => $this->faker->text()
            ]
        );

        $response->assertStatus(201);

        // Create another with the same name
        $response = $this->postSubcategory(
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
    public function create_subcategory_forbidden_when_category_id_invalid(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAndReturnResourceTypeId();

        $response = $this->postSubcategory(
            $resource_type_id,
            'wwwwwwwwww',
            [
                'name' => $this->faker->text(200),
            ]
        );

        $response->assertStatus(403);
    }

    /** @test */
    public function create_subcategory_success(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAndReturnResourceTypeId();
        $category_id = $this->createAndReturnCategoryId($resource_type_id);

        $response = $this->postSubcategory(
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
    public function delete_subcategory_success(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAndReturnResourceTypeId();
        $category_id = $this->createAndReturnCategoryId($resource_type_id);
        $subcategory_id = $this->createAndReturnSubcategoryId($resource_type_id, $category_id);

        $response = $this->deleteSubcategory(
            $resource_type_id,
            $category_id,
            $subcategory_id
        );

        $response->assertStatus(204);
    }

    /** @test */
    public function update_subcategory_fails_extra_fields_in_payload(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAndReturnResourceTypeId();
        $category_id = $this->createAndReturnCategoryId($resource_type_id);
        $subcategory_id = $this->createAndReturnSubcategoryId($resource_type_id, $category_id);

        $response = $this->patchSubcategory(
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
    public function update_subcategory_fails_no_payload(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAndReturnResourceTypeId();
        $category_id = $this->createAndReturnCategoryId($resource_type_id);
        $subcategory_id = $this->createAndReturnSubcategoryId($resource_type_id, $category_id);

        $response = $this->patchSubcategory(
            $resource_type_id,
            $category_id,
            $subcategory_id,
            []
        );

        $response->assertStatus(400);
    }

    /** @test */
    public function update_subcategory_fails_non_unique_name(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAndReturnResourceTypeId();
        $category_id = $this->createAndReturnCategoryId($resource_type_id);

        // Create first subcategory
        $name = $this->faker->text(200);

        $response = $this->postSubcategory(
            $resource_type_id,
            $category_id,
            [
                'name' => $name,
                'description' => $this->faker->text(200)
            ]
        );

        $response->assertStatus(201);

        // Create second subcategory
        $response = $this->postSubcategory(
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
        $response = $this->patchSubcategory(
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
    public function update_subcategory_description_success(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAndReturnResourceTypeId();
        $category_id = $this->createAndReturnCategoryId($resource_type_id);
        $subcategory_id = $this->createAndReturnSubcategoryId($resource_type_id, $category_id);

        $response = $this->patchSubcategory(
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
    public function update_subcategory_name_success(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAndReturnResourceTypeId();
        $category_id = $this->createAndReturnCategoryId($resource_type_id);
        $subcategory_id = $this->createAndReturnSubcategoryId($resource_type_id, $category_id);

        $response = $this->patchSubcategory(
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
    public function update_subcategory_success(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAndReturnResourceTypeId();
        $category_id = $this->createAndReturnCategoryId($resource_type_id);
        $subcategory_id = $this->createAndReturnSubcategoryId($resource_type_id, $category_id);

        $response = $this->patchSubcategory(
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
