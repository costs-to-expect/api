<?php

namespace Tests\Feature\Http\Controllers;

use App\User;
use Tests\TestCase;

class ResourceTypeManageTest extends TestCase
{
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
}
