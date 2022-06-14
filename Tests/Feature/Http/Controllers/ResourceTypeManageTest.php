<?php

namespace Tests\Feature\Http\Controllers;

use App\User;
use Tests\TestCase;

class ResourceTypeManageTest extends TestCase
{
    /** @test */
    public function create_resource_type_fails_data_field_not_valid_json(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->postResourceType(
            [
                'name' => $this->faker->text(200),
                'description' => $this->faker->text(200),
                'data' => '{"field": "value}',
                'item_type_id' => 'OqZwKX16bW',
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function create_resource_type_fails_item_type_invalid(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->postResourceType(
            [
                'name' => $this->faker->text(200),
                'description' => $this->faker->text(200),
                'item_type_id' => 'OqZwKX16bg'
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function create_resource_type_fails_no_description_in_payload(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->postResourceType(
            [
                'name' => $this->faker->text(200),
                'item_type_id' => 'OqZwKX16bW'
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function create_resource_type_fails_no_name_in_payload(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->postResourceType(
            [
                'description' => $this->faker->text(200),
                'item_type_id' => 'OqZwKX16bW'
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function create_resource_type_fails_no_payload(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->postResourceType(
            []
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function create_resource_type_fails_non_unique_name(): void
    {
        $this->actingAs(User::find(1));

        $name = $this->faker->text(255);

        $response = $this->postResourceType(
            [
                'name' => $name,
                'description' => $this->faker->text,
                'item_type_id' => 'OqZwKX16bW',
                'public' => false
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonIsResourceType($response->content());

        // Create the second with the same name
        $response = $this->postResourceType(
            [
                'name' => $name,
                'description' => $this->faker->text,
                'item_type_id' => 'OqZwKX16bW',
                'public' => false
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function create_resource_type_fails_not_signed_in(): void
    {
        $response = $this->postResourceType(
            []
        );

        $response->assertStatus(403);
    }

    /** @test */
    public function create_resource_type_success(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->postResourceType(
            [
                'name' => $this->faker->text(255),
                'description' => $this->faker->text,
                'item_type_id' => 'OqZwKX16bW',
                'public' => false
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonIsResourceType($response->content());
    }

    /** @test */
    public function create_resource_type_success_include_data_field(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->postResourceType(
            [
                'name' => $this->faker->text(255),
                'description' => $this->faker->text,
                'data' => '{"field": "value"}',
                'item_type_id' => 'OqZwKX16bW',
                'public' => false
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonIsResourceType($response->content());
    }

    /** @test */
    public function delete_resource_type_success(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->postResourceType(
            [
                'name' => $this->faker->text(255),
                'description' => $this->faker->text,
                'item_type_id' => 'OqZwKX16bW',
                'public' => false
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonIsResourceType($response->content());

        $id = $response->json('id');

        $response = $this->deleteResourceType($id);

        $response->assertStatus(204);
    }

    /** @test */
    public function update_resource_type_fails_extra_fields_in_payload(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->postResourceType(
            [
                'name' => $this->faker->text(255),
                'description' => $this->faker->text,
                'item_type_id' => 'OqZwKX16bW',
                'public' => false
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonIsResourceType($response->content());

        $id = $response->json('id');

        $response = $this->patchResourceType(
            $id,
            [
                'extra' => $this->faker->text(100)
            ]
        );

        $response->assertStatus(400);
    }

    /** @test */
    public function update_resource_type_fails_no_payload(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->postResourceType(
            [
                'name' => $this->faker->text(255),
                'description' => $this->faker->text,
                'item_type_id' => 'OqZwKX16bW',
                'public' => false
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonIsResourceType($response->content());

        $id = $response->json('id');

        $response = $this->patchResourceType(
            $id,
            []
        );

        $response->assertStatus(400);
    }

    /** @test */
    public function update_resource_type_fails_non_unique_name(): void
    {
        $this->actingAs(User::find(1));

        $name = $this->faker->text(255);

        // Create the first resource type
        $response = $this->postResourceType(
            [
                'name' => $name,
                'description' => $this->faker->text(255),
                'item_type_id' => 'OqZwKX16bW',
                'public' => false
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonIsResourceType($response->content());

        // Create the second resource type
        $response = $this->postResourceType(
            [
                'name' => $this->faker->text(255),
                'description' => $this->faker->text(255),
                'item_type_id' => 'OqZwKX16bW',
                'public' => false
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonIsResourceType($response->content());

        $id = $response->json('id');

        // Update with same name as the first
        $response = $this->patchResourceType(
            $id,
            [
                'name' => $name
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function update_resource_type_success(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->postResourceType(
            [
                'name' => $this->faker->text(255),
                'description' => $this->faker->text,
                'item_type_id' => 'OqZwKX16bW',
                'public' => false
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonIsResourceType($response->content());

        $id = $response->json('id');

        $response = $this->patchResourceType(
            $id,
            [
                'name' => $this->faker->text(100)
            ]
        );

        $response->assertStatus(204);
    }
}
