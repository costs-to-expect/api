<?php

namespace Tests\Feature\Http\Controllers;

use App\HttpRequest\Hash;
use App\Models\ResourceType;
use App\User;
use Tests\TestCase;

class ResourceManageTest extends TestCase
{
    /** @test */
    public function createResourceFailsDataFieldNotValidJson(): void
    {
        $this->actingAs(User::find(1));

        $id = $this->createAndReturnResourceTypeId();

        $response = $this->postResource(
            $id,
            [
                'name' => $this->faker->text(200),
                'description' => $this->faker->text(200),
                'data' => '{"field"=>true}',
                'item_subtype_id' => 'a56kbWV82n'
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createResourceFailsItemSubtypeInvalid(): void
    {
        $this->actingAs(User::find(1));

        $id = $this->createAndReturnResourceTypeId();

        $response = $this->postResource(
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
    public function createResourceFailsNoDescriptionInPayload(): void
    {
        $this->actingAs(User::find(1));

        $id = $this->createAndReturnResourceTypeId();

        $response = $this->postResource(
            $id,
            [
                'name' => $this->faker->text(200),
                'data' => '{"field"=>"data"}',
                'item_subtype_id' => 'a56kbW'
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createResourceFailsNoNameInPayload(): void
    {
        $this->actingAs(User::find(1));

        $id = $this->createAndReturnResourceTypeId();

        $response = $this->postResource(
            $id,
            [
                'description' => $this->faker->text(200),
                'data' => '{"field"=>"data"}',
                'item_subtype_id' => 'a56kbW'
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createResourceFailsNoPayload(): void
    {
        $this->actingAs(User::find(1));

        $id = $this->createAndReturnResourceTypeId();

        $response = $this->postResource(
            $id,
            []
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createResourceFailsNoPermissionToResourceType(): void
    {
        $this->actingAs(User::find($this->getARandomUser()->id)); // Random user

        $resource_type = ResourceType::query()
            ->join('permitted_user', 'resource_type.id', '=', 'permitted_user.resource_type_id')
            ->where('permitted_user.user_id', '=', 1)
            ->first();

        if ($resource_type !== null) {

            $resource_type_id = (new Hash())->encode('resource-type', $resource_type->id);

            $response = $this->postResource(
                $resource_type_id,
                [
                    'name' => $this->faker->text(200),
                    'description' => $this->faker->text(200),
                    'item_subtype_id' => 'a56kbWV82n'
                ]
            );

            $response->assertStatus(403);

        } else {
            $this->fail('Unable to fetch a resource type for testing in');
        }
    }

    /** @test */
    public function createResourceFailsNonUniqueName(): void
    {
        $this->actingAs(User::find(1));

        $id = $this->createAndReturnResourceTypeId();

        $name = $this->faker->text(200);

        $response = $this->postResource(
            $id,
            [
                'name' => $name,
                'description' => $this->faker->text(200),
                'item_subtype_id' => 'a56kbWV82n'
            ]
        );

        $response->assertStatus(201);

        // Create again with non-unique name for resource type
        $response = $this->postResource(
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
    public function createResourceSuccess(): void
    {
        $this->actingAs(User::find(1));

        $id = $this->createAndReturnResourceTypeId();

        $response = $this->postResource(
            $id,
            [
                'name' => $this->faker->text(200),
                'description' => $this->faker->text(200),
                'item_subtype_id' => 'a56kbWV82n'
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonIsResource($response->content());
    }

    /** @test */
    public function createResourceSuccessIncludeDataField(): void
    {
        $this->actingAs(User::find(1));

        $id = $this->createAndReturnResourceTypeId();

        $response = $this->postResource(
            $id,
            [
                'name' => $this->faker->text(200),
                'description' => $this->faker->text(200),
                'data' => '{"field":true}',
                'item_subtype_id' => 'a56kbWV82n'
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonIsResource($response->content());
    }

    /** @test */
    public function deleteResourceSuccess(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAndReturnResourceTypeId();
        $id = $this->createAndReturnResourceId($resource_type_id);

        $response = $this->deleteResource($resource_type_id, $id);

        $response->assertStatus(204);
    }

    /** @test */
    public function updateResourceFailsExtraFieldsInPayload(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAndReturnResourceTypeId();
        $resource_id = $this->createAndReturnResourceId($resource_type_id);

        $response = $this->patchResource(
            $resource_type_id,
            $resource_id,
            [
                'extra' => $this->faker->text(100)
            ]
        );

        $response->assertStatus(400);
    }

    /** @test */
    public function updateResourceFailsNonPayload(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAndReturnResourceTypeId();
        $resource_id = $this->createAndReturnResourceId($resource_type_id);

        $response = $this->patchResource(
            $resource_type_id,
            $resource_id,
            []
        );

        $response->assertStatus(400);
    }

    /** @test */
    public function updateResourceFailsNonUniqueName(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAndReturnResourceTypeId();

        // Create first resource
        $name = $this->faker->text(200);
        $response = $this->postResource(
            $resource_type_id,
            [
                'name' => $name,
                'description' => $this->faker->text(200),
                'item_subtype_id' => 'a56kbWV82n'
            ]
        );

        $response->assertStatus(201);

        // Create second resource
        $response = $this->postResource(
            $resource_type_id,
            [
                'name' => $this->faker->text(200),
                'description' => $this->faker->text(200),
                'item_subtype_id' => 'a56kbWV82n'
            ]
        );

        $response->assertStatus(201);
        $resource_id = $response->json('id');

        // Set name of second resource to first name
        $response = $this->patchResource(
            $resource_type_id,
            $resource_id,
            [
                'name' => $name
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function updateResourceSuccess(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAndReturnResourceTypeId();
        $resource_id = $this->createAndReturnResourceId($resource_type_id);

        $response = $this->patchResource(
            $resource_type_id,
            $resource_id,
            [
                'name' => $this->faker->text(25)
            ]
        );

        $response->assertStatus(204);
    }
}
