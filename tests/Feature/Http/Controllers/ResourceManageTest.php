<?php

namespace Tests\Feature\Http\Controllers;

use App\User;
use Tests\TestCase;

class ResourceManageTest extends TestCase
{
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
    }
}
