<?php

namespace Tests\Feature\Http\Controllers;

use App\HttpRequest\Hash;
use App\Models\ResourceType;
use App\User;
use Tests\TestCase;

final class ResourceTest extends TestCase
{
    /** @test */
    public function createAllocatedExpenseResourceFailsDataFieldNotValidJson(): void
    {
        $this->actingAs(User::find(1));

        $id = $this->createAllocatedExpenseResourceType();

        $response = $this->createResource(
            $id,
            [
                'name' => $this->faker->text(200),
                'description' => $this->faker->text(200),
                'data' => '{"field"=>true}',
                'item_subtype_id' => $this->item_subtypes['allocated-expense']['default']
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createAllocatedExpenseResourceFailsItemSubtypeInvalid(): void
    {
        $this->actingAs(User::find(1));

        $id = $this->createAllocatedExpenseResourceType();

        $response = $this->createResource(
            $id,
            [
                'name' => $this->faker->text(200),
                'description' => $this->faker->text(200),
                'data' => '{"field"=>"data"}',
                'item_subtype_id' => 'a56kbW'
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createAllocatedExpenseResourceFailsNoDescriptionInPayload(): void
    {
        $this->actingAs(User::find(1));

        $id = $this->createAllocatedExpenseResourceType();

        $response = $this->createResource(
            $id,
            [
                'name' => $this->faker->text(200),
                'data' => '{"field"=>"data"}',
                'item_subtype_id' => $this->item_subtypes['allocated-expense']['default']
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createAllocatedExpenseResourceFailsNoNameInPayload(): void
    {
        $this->actingAs(User::find(1));

        $id = $this->createAllocatedExpenseResourceType();

        $response = $this->createResource(
            $id,
            [
                'description' => $this->faker->text(200),
                'data' => '{"field"=>"data"}',
                'item_subtype_id' => $this->item_subtypes['allocated-expense']['default']
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createAllocatedExpenseResourceFailsNoPayload(): void
    {
        $this->actingAs(User::find(1));

        $id = $this->createAllocatedExpenseResourceType();

        $response = $this->createResource(
            $id,
            []
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createAllocatedExpenseResourceFailsNoPermissionToResourceType(): void
    {
        $this->actingAs(User::find($this->fetchRandomUser()->id)); // Random user

        $resource_type = ResourceType::query()
            ->join('permitted_user', 'resource_type.id', '=', 'permitted_user.resource_type_id')
            ->where('permitted_user.user_id', '=', 1)
            ->first();

        if ($resource_type !== null) {

            $resource_type_id = (new Hash())->encode('resource-type', $resource_type->id);

            $response = $this->createResource(
                $resource_type_id,
                [
                    'name' => $this->faker->text(200),
                    'description' => $this->faker->text(200),
                    'item_subtype_id' => $this->item_subtypes['allocated-expense']['default']
                ]
            );

            $response->assertStatus(403);

        } else {
            $this->fail('Unable to fetch a resource type for testing in');
        }
    }

    /** @test */
    public function createAllocatedExpenseResourceFailsNonUniqueName(): void
    {
        $this->actingAs(User::find(1));

        $id = $this->createAllocatedExpenseResourceType();

        $name = $this->faker->text(200);

        $response = $this->createResource(
            $id,
            [
                'name' => $name,
                'description' => $this->faker->text(200),
                'item_subtype_id' => $this->item_subtypes['allocated-expense']['default']
            ]
        );

        $response->assertStatus(201);

        // Create again with non-unique name for resource type
        $response = $this->createResource(
            $id,
            [
                'name' => $name,
                'description' => $this->faker->text(200),
                'item_subtype_id' => 'a56kbWV82n'
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createAllocatedExpenseResourceSuccess(): void
    {
        $this->actingAs(User::find(1));

        $id = $this->createAllocatedExpenseResourceType();

        $response = $this->createResource(
            $id,
            [
                'name' => $this->faker->text(200),
                'description' => $this->faker->text(200),
                'item_subtype_id' => $this->item_subtypes['allocated-expense']['default']
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonMatchesResourceSchema($response->content());
    }

    /** @test */
    public function createAllocatedExpenseResourceSuccessIncludeDataField(): void
    {
        $this->actingAs(User::find(1));

        $id = $this->createAllocatedExpenseResourceType();

        $response = $this->createResource(
            $id,
            [
                'name' => $this->faker->text(200),
                'description' => $this->faker->text(200),
                'data' => '{"field":true}',
                'item_subtype_id' => $this->item_subtypes['allocated-expense']['default']
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonMatchesResourceSchema($response->content());
    }

    /** @test */
    public function deleteAllocatedExpenseResourceSuccess(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $id = $this->createAllocatedExpenseResource($resource_type_id);

        $response = $this->deleteResource($resource_type_id, $id);

        $response->assertStatus(204);
    }

    /** @test */
    public function updateAllocatedExpenseResourceFailsExtraFieldsInPayload(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $resource_id = $this->createAllocatedExpenseResource($resource_type_id);

        $response = $this->updatedResource(
            $resource_type_id,
            $resource_id,
            [
                'extra' => $this->faker->text(100)
            ]
        );

        $response->assertStatus(400);
    }

    /** @test */
    public function updateAllocatedExpenseResourceFailsNonPayload(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $resource_id = $this->createAllocatedExpenseResource($resource_type_id);

        $response = $this->updatedResource(
            $resource_type_id,
            $resource_id,
            []
        );

        $response->assertStatus(400);
    }

    /** @test */
    public function updateAllocatedExpenseResourceFailsNonUniqueName(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();

        // Create first resource
        $name = $this->faker->text(200);
        $response = $this->createResource(
            $resource_type_id,
            [
                'name' => $name,
                'description' => $this->faker->text(200),
                'item_subtype_id' => $this->item_subtypes['allocated-expense']['default']
            ]
        );

        $response->assertStatus(201);

        // Create second resource
        $response = $this->createResource(
            $resource_type_id,
            [
                'name' => $this->faker->text(200),
                'description' => $this->faker->text(200),
                'item_subtype_id' => $this->item_subtypes['allocated-expense']['default']
            ]
        );

        $response->assertStatus(201);
        $resource_id = $response->json('id');

        // Set name of second resource to first name
        $response = $this->updatedResource(
            $resource_type_id,
            $resource_id,
            [
                'name' => $name
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function updateAllocatedExpenseResourceSuccess(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $resource_id = $this->createAllocatedExpenseResource($resource_type_id);

        $response = $this->updatedResource(
            $resource_type_id,
            $resource_id,
            [
                'name' => $this->faker->text(25)
            ]
        );

        $response->assertStatus(204);
    }
}
