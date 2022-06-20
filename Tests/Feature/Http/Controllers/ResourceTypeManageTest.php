<?php

namespace Tests\Feature\Http\Controllers;

use App\User;
use Tests\TestCase;

final class ResourceTypeManageTest extends TestCase
{
    /** @test */
    public function createResourceTypeFailsDataFieldNotValidJson(): void
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
    public function createResourceTypeFailsItemTypeInvalid(): void
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
    public function createResourceTypeFailsNoDescriptionInPayload(): void
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
    public function createResourceTypeFailsNoNameInPayload(): void
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
    public function createResourceTypeFailsNoPayload(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->postResourceType(
            []
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createResourceTypeFailsNonUniqueName(): void
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
    public function createResourceTypeFailsNotSignedIn(): void
    {
        $response = $this->postResourceType(
            []
        );

        $response->assertStatus(403);
    }

    /** @test */
    public function createResourceTypeSuccess(): void
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
    public function createResourceTypeSuccessIncludeDataField(): void
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
    public function deleteResourceTypeSuccess(): void
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
    public function updateResourceTypeFailsExtraFieldsInPayload(): void
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
    public function updateResourceTypeFailsNoPayload(): void
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
    public function updateResourceTypeFailsNonUniqueName(): void
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
    public function updateResourceTypeSuccess(): void
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
