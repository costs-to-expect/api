<?php

namespace Tests\Action\Http\Controllers;

use App\HttpRequest\Hash;
use App\Models\ResourceType;
use App\User;
use Tests\TestCase;

final class CategoryManageTest extends TestCase
{
    /** @test */
    public function createCategoryFailsNoDescriptionInPayload(): void
    {
        $this->actingAs(User::find(1));

        $id = $this->createAllocatedExpenseResourceType();

        $response = $this->createRequestedCategory(
            $id,
            [
                'name' => $this->faker->text(200),
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createCategoryFailsNoNameInPayload(): void
    {
        $this->actingAs(User::find(1));

        $id = $this->createAllocatedExpenseResourceType();

        $response = $this->createRequestedCategory(
            $id,
            [
                'description' => $this->faker->text(200),
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createCategoryFailsNoPermissionToResourceType(): void
    {
        $this->actingAs(User::find($this->fetchRandomUser()->id)); // Random user

        $resource_type = ResourceType::query()
            ->join('permitted_user', 'resource_type.id', '=', 'permitted_user.resource_type_id')
            ->where('permitted_user.user_id', '=', 1)
            ->first();

        if ($resource_type !== null) {

            $resource_type_id = (new Hash())->encode('resource-type', $resource_type->id);

            $response = $this->createRequestedCategory(
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
    public function createCategoryFailsNoPayload(): void
    {
        $this->actingAs(User::find(1));

        $id = $this->createAllocatedExpenseResourceType();

        $response = $this->createRequestedCategory(
            $id,
            []
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createCategoryFailsNonUniqueName(): void
    {
        $this->actingAs(User::find(1));

        $id = $this->createAllocatedExpenseResourceType();

        $name = $this->faker->text(200);

        $response = $this->createRequestedCategory(
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
    public function createCategorySuccess(): void
    {
        $this->actingAs(User::find(1));

        $id = $this->createAllocatedExpenseResourceType();

        $response = $this->createRequestedCategory(
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
    public function deleteCategorySuccess(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $id = $this->createRandomCategory($resource_type_id);

        $response = $this->deleteRequestedCategory($resource_type_id, $id);

        $response->assertStatus(204);
    }

    /** @test */
    public function updateCategoryFailsExtraFieldsInPayload(): void
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
    public function updateCategoryFailsNoPayload(): void
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
    public function updateCategoryFailsNonUniqueName(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();

        // Create first category
        $name = $this->faker->text(200);
        $response = $this->createRequestedCategory(
            $resource_type_id,
            [
                'name' => $name,
                'description' => $this->faker->text(200)
            ]
        );

        $response->assertStatus(201);

        // Create second category
        $response = $this->createRequestedCategory(
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
    public function updateCategorySuccess(): void
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
}
