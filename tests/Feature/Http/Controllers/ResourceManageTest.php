<?php

namespace Tests\Feature\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ResourceManageTest extends TestCase
{
    /** @test */
    public function create_resource_fails_data_field_not_valid_json(): void
    {
        $this->actingAs(User::find(1));

        $id = $this->helperCreateResourceType();

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
    public function create_resource_fails_item_subtype_invalid(): void
    {
        $this->actingAs(User::find(1));

        $id = $this->helperCreateResourceType();

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
    public function create_resource_fails_no_description_in_payload(): void
    {
        $this->actingAs(User::find(1));

        $id = $this->helperCreateResourceType();

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
    public function create_resource_fails_no_name_in_payload(): void
    {
        $this->actingAs(User::find(1));

        $id = $this->helperCreateResourceType();

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
    public function create_resource_fails_no_payload(): void
    {
        $this->actingAs(User::find(1));

        $id = $this->helperCreateResourceType();

        $response = $this->postResource(
            $id,
            []
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function create_resource_fails_non_unique_name(): void
    {
        $this->actingAs(User::find(1));

        $id = $this->helperCreateResourceType();

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
    public function create_resource_success(): void
    {
        $this->actingAs(User::find(1));

        $id = $this->helperCreateResourceType();

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
    public function create_resource_success_include_data_field(): void
    {
        $this->actingAs(User::find(1));

        $id = $this->helperCreateResourceType();

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
    public function delete_resource_success(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->helperCreateResourceType();
        $id = $this->helperCreateResource($resource_type_id);

        $response = $this->deleteResource($resource_type_id, $id);

        $response->assertStatus(204);
    }

    /** @test */
    public function update_resource_fails_extra_fields_in_payload(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->helperCreateResourceType();
        $resource_id = $this->helperCreateResource($resource_type_id);

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
    public function update_resource_fails_non_payload(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->helperCreateResourceType();
        $resource_id = $this->helperCreateResource($resource_type_id);

        $response = $this->patchResource(
            $resource_type_id,
            $resource_id,
            []
        );

        $response->assertStatus(400);
    }

    /** @test */
    public function update_resource_fails_non_unique_name(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->helperCreateResourceType();

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
    public function update_resource_success(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->helperCreateResourceType();
        $resource_id = $this->helperCreateResource($resource_type_id);

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
