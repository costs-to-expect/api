<?php

namespace Tests\Feature\Http\Controllers;

use App\User;
use Tests\TestCase;

class ResourceManageTest extends TestCase
{
    /** @test */
    public function create_resource_fails_data_field_not_valid_json(): void
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
        $id = $response->json('id');

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
    public function create_resource_success(): void
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
        $id = $response->json('id');

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

        $response = $this->postResourceType(
            [
                'name' => $this->faker->text(255),
                'description' => $this->faker->text,
                'data' => '{"field":true}',
                'item_type_id' => 'OqZwKX16bW',
                'public' => false
            ]
        );

        $response->assertStatus(201);
        $id = $response->json('id');

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
}
