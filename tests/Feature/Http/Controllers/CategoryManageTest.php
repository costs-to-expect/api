<?php

namespace Tests\Feature\Http\Controllers;

use App\HttpRequest\Hash;
use App\Models\ResourceType;
use App\User;
use Tests\TestCase;

class CategoryManageTest extends TestCase
{
    /** @test */
    public function create_category_fails_no_description_in_payload(): void
    {
        $this->actingAs(User::find(1));

        $id = $this->createAndReturnResourceTypeId();

        $response = $this->postCategory(
            $id,
            [
                'name' => $this->faker->text(200),
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function create_category_fails_no_name_in_payload(): void
    {
        $this->actingAs(User::find(1));

        $id = $this->createAndReturnResourceTypeId();

        $response = $this->postCategory(
            $id,
            [
                'description' => $this->faker->text(200),
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function create_category_fails_no_permission_to_resource_type(): void
    {
        $this->actingAs(User::find($this->getARandomUser()->id)); // Random user

        $resource_type = ResourceType::query()
            ->join('permitted_user', 'resource_type.id', '=', 'permitted_user.resource_type_id')
            ->where('permitted_user.user_id', '=', 1)
            ->first();

        if ($resource_type !== null) {

            $resource_type_id = (new Hash())->encode('resource-type', $resource_type->id);

            $response = $this->postCategory(
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
    public function create_category_fails_no_payload(): void
    {
        $this->actingAs(User::find(1));

        $id = $this->createAndReturnResourceTypeId();

        $response = $this->postCategory(
            $id,
            []
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function create_category_fails_non_unique_name(): void
    {
        $this->actingAs(User::find(1));

        $id = $this->createAndReturnResourceTypeId();

        $name = $this->faker->text(200);

        $response = $this->postCategory(
            $id,
            [
                'name' => $name,
                'description' => $this->faker->text(200),
            ]
        );

        $response->assertStatus(201);

        // Create again with non-unique name for resource type
        $response = $this->postResource(
            $id,
            [
                'name' => $name,
                'description' => $this->faker->text(200),
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function create_category_success(): void
    {
        $this->actingAs(User::find(1));

        $id = $this->createAndReturnResourceTypeId();

        $response = $this->postCategory(
            $id,
            [
                'name' => $this->faker->text(200),
                'description' => $this->faker->text(200),
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonIsCategory($response->content());
    }

    /** @test */
    public function delete_category_success(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAndReturnResourceTypeId();
        $id = $this->createAndReturnCategoryId($resource_type_id);

        $response = $this->deleteCategory($resource_type_id, $id);

        $response->assertStatus(204);
    }

    /** @test */
    public function update_category_fails_extra_fields_in_payload(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAndReturnResourceTypeId();
        $resource_id = $this->createAndReturnCategoryId($resource_type_id);

        $response = $this->patchCategory(
            $resource_type_id,
            $resource_id,
            [
                'extra' => $this->faker->text(100)
            ]
        );

        $response->assertStatus(400);
    }

    /** @test */
    public function update_category_fails_non_payload(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAndReturnResourceTypeId();
        $resource_id = $this->createAndReturnCategoryId($resource_type_id);

        $response = $this->patchCategory(
            $resource_type_id,
            $resource_id,
            []
        );

        $response->assertStatus(400);
    }

    /** @test */
    public function update_category_fails_non_unique_name(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAndReturnResourceTypeId();

        // Create first category
        $name = $this->faker->text(200);
        $response = $this->postCategory(
            $resource_type_id,
            [
                'name' => $name,
                'description' => $this->faker->text(200)
            ]
        );

        $response->assertStatus(201);

        // Create second category
        $response = $this->postCategory(
            $resource_type_id,
            [
                'name' => $this->faker->text(200),
                'description' => $this->faker->text(200)
            ]
        );

        $response->assertStatus(201);
        $category_id = $response->json('id');

        // Attempt to set name of second category to first name
        $response = $this->patchCategory(
            $resource_type_id,
            $category_id,
            [
                'name' => $name
            ]
        );

        echo $response->content();

        $response->assertStatus(422);
    }

    /** @test */
    public function update_category_success(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAndReturnResourceTypeId();
        $resource_id = $this->createAndReturnCategoryId($resource_type_id);

        $response = $this->patchCategory(
            $resource_type_id,
            $resource_id,
            [
                'name' => $this->faker->text(25)
            ]
        );

        $response->assertStatus(204);
    }
}
