<?php

namespace Tests\Feature\Http\Controllers;

use App\User;
use Tests\TestCase;

class ResourceTypeManageTest extends TestCase
{
    /** @test */
    public function create_resource_type_fails_item_type_invalid(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->post(
            route('resource-type.create'),
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

        $response = $this->post(
            route('resource-type.create'),
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

        $response = $this->post(
            route('resource-type.create'),
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

        $response = $this->post(
            route('resource-type.create'),
            []
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function create_resource_type_fails_not_signed_in(): void
    {
        $response = $this->post(
            route('resource-type.create'),
            []
        );

        $response->assertStatus(403);
    }

    /** @test */
    public function create_resource_type_success(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->post(
            route('resource-type.create'),
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
    public function update_resource_type_fails_no_payload(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->post(
            route('resource-type.create'),
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

        $response = $this->patch(
            route('resource-type.update', ['resource_type_id' => $id]),
            [
            ]
        );

        $response->assertStatus(400);
    }
}
