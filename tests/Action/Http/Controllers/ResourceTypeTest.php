<?php

namespace Tests\Action\Http\Controllers;

use Tests\TestCase;

final class ResourceTypeTest extends TestCase
{
    /** @test */
    public function createAllocatedExpenseResourceTypeFailsDataFieldNotValidJson(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->postToResourceTypeCreate(
            [
                'name' => $this->faker->text(200),
                'description' => $this->faker->text(200),
                'data' => '{"field": "value}',
                'item_type_id' => $this->item_types['allocated-expense'],
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createAllocatedExpenseResourceTypeFailsItemTypeInvalid(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->postToResourceTypeCreate(
            [
                'name' => $this->faker->text(200),
                'description' => $this->faker->text(200),
                'item_type_id' => 'OqZwKX16bg'
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createAllocatedExpenseResourceTypeFailsNoDescriptionInPayload(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->postToResourceTypeCreate(
            [
                'name' => $this->faker->text(200),
                'item_type_id' => $this->item_types['allocated-expense']
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createAllocatedExpenseResourceTypeFailsNoNameInPayload(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->postToResourceTypeCreate(
            [
                'description' => $this->faker->text(200),
                'item_type_id' => $this->item_types['allocated-expense']
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createAllocatedExpenseResourceTypeFailsNonUniqueName(): void
    {
        $this->actingAs($this->createUser());

        $name = $this->faker->text(255);

        $response = $this->postToResourceTypeCreate(
            [
                'name' => $name,
                'description' => $this->faker->text,
                'item_type_id' => $this->item_types['allocated-expense'],
                'public' => false
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonMatchesResourceTypeSchema($response->content());

        // Create the second with the same name
        $response = $this->postToResourceTypeCreate(
            [
                'name' => $name,
                'description' => $this->faker->text,
                'item_type_id' => $this->item_types['allocated-expense'],
                'public' => false
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createAllocatedExpenseResourceTypeSuccess(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->postToResourceTypeCreate(
            [
                'name' => $this->faker->text(255),
                'description' => $this->faker->text,
                'item_type_id' => $this->item_types['allocated-expense'],
                'public' => false
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonMatchesResourceTypeSchema($response->content());
    }

    /** @test */
    public function createAllocatedExpenseResourceTypeSuccessIncludeDataField(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->postToResourceTypeCreate(
            [
                'name' => $this->faker->text(255),
                'description' => $this->faker->text,
                'data' => '{"field": "value"}',
                'item_type_id' => $this->item_types['allocated-expense'],
                'public' => false
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonMatchesResourceTypeSchema($response->content());
    }

    /** @test */
    public function createBudgetProResourceTypeSuccess(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->postToResourceTypeCreate(
            [
                'name' => $this->faker->text(255),
                'description' => $this->faker->text,
                'item_type_id' => $this->item_types['budget-pro'],
                'public' => false
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonMatchesResourceTypeSchema($response->content());
    }

    /** @test */
    public function createBudgetResourceTypeSuccess(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->postToResourceTypeCreate(
            [
                'name' => $this->faker->text(255),
                'description' => $this->faker->text,
                'item_type_id' => $this->item_types['budget'],
                'public' => false
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonMatchesResourceTypeSchema($response->content());
    }

    /** @test */
    public function createGameResourceTypeSuccess(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->postToResourceTypeCreate(
            [
                'name' => $this->faker->text(255),
                'description' => $this->faker->text,
                'item_type_id' => $this->item_types['game'],
                'public' => false
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonMatchesResourceTypeSchema($response->content());
    }

    /** @test */
    public function createResourceTypeFailsNoPayload(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->postToResourceTypeCreate(
            []
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createResourceTypeFailsNotSignedIn(): void
    {
        $response = $this->postToResourceTypeCreate(
            []
        );

        $response->assertStatus(403);
    }

    /** @test */
    public function deleteAllocatedExpenseResourceTypeSuccess(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->postToResourceTypeCreate(
            [
                'name' => $this->faker->text(255),
                'description' => $this->faker->text,
                'item_type_id' => $this->item_types['allocated-expense'],
                'public' => false
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonMatchesResourceTypeSchema($response->content());

        $id = $response->json('id');

        $response = $this->deleteToResourceTypeDelete($id);

        $response->assertStatus(204);
    }

    /** @test */
    public function deleteBudgetProResourceTypeSuccess(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->postToResourceTypeCreate(
            [
                'name' => $this->faker->text(255),
                'description' => $this->faker->text,
                'item_type_id' => $this->item_types['budget-pro'],
                'public' => false
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonMatchesResourceTypeSchema($response->content());

        $id = $response->json('id');

        $response = $this->deleteToResourceTypeDelete($id);

        $response->assertStatus(204);
    }

    /** @test */
    public function deleteBudgetResourceTypeSuccess(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->postToResourceTypeCreate(
            [
                'name' => $this->faker->text(255),
                'description' => $this->faker->text,
                'item_type_id' => $this->item_types['budget'],
                'public' => false
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonMatchesResourceTypeSchema($response->content());

        $id = $response->json('id');

        $response = $this->deleteToResourceTypeDelete($id);

        $response->assertStatus(204);
    }

    /** @test */
    public function deleteGameResourceTypeSuccess(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->postToResourceTypeCreate(
            [
                'name' => $this->faker->text(255),
                'description' => $this->faker->text,
                'item_type_id' => $this->item_types['game'],
                'public' => false
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonMatchesResourceTypeSchema($response->content());

        $id = $response->json('id');

        $response = $this->deleteToResourceTypeDelete($id);

        $response->assertStatus(204);
    }

    /** @test */
    public function updateAllocatedExpenseResourceTypeFailsExtraFieldsInPayload(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->postToResourceTypeCreate(
            [
                'name' => $this->faker->text(255),
                'description' => $this->faker->text,
                'item_type_id' => $this->item_types['allocated-expense'],
                'public' => false
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonMatchesResourceTypeSchema($response->content());

        $id = $response->json('id');

        $response = $this->patchToResourceTypeUpdate(
            $id,
            [
                'extra' => $this->faker->text(100)
            ]
        );

        $response->assertStatus(400);
    }

    /** @test */
    public function updateAllocatedExpenseResourceTypeFailsNonUniqueName(): void
    {
        $this->actingAs($this->createUser());

        $name = $this->faker->text(255);

        // Create the first resource type
        $response = $this->postToResourceTypeCreate(
            [
                'name' => $name,
                'description' => $this->faker->text(255),
                'item_type_id' => $this->item_types['allocated-expense'],
                'public' => false
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonMatchesResourceTypeSchema($response->content());

        // Create the second resource type
        $response = $this->postToResourceTypeCreate(
            [
                'name' => $this->faker->text(255),
                'description' => $this->faker->text(255),
                'item_type_id' => $this->item_types['allocated-expense'],
                'public' => false
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonMatchesResourceTypeSchema($response->content());

        $id = $response->json('id');

        // Update with the same name as the first
        $response = $this->patchToResourceTypeUpdate(
            $id,
            [
                'name' => $name
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function updateAllocatedExpenseResourceTypeFailsNoPayload(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->postToResourceTypeCreate(
            [
                'name' => $this->faker->text(255),
                'description' => $this->faker->text,
                'item_type_id' => $this->item_types['allocated-expense'],
                'public' => false
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonMatchesResourceTypeSchema($response->content());

        $id = $response->json('id');

        $response = $this->patchToResourceTypeUpdate(
            $id,
            []
        );

        $response->assertStatus(400);
    }

    /** @test */
    public function updateAllocatedExpenseResourceTypeSuccess(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->postToResourceTypeCreate(
            [
                'name' => $this->faker->text(255),
                'description' => $this->faker->text,
                'item_type_id' => $this->item_types['allocated-expense'],
                'public' => false
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonMatchesResourceTypeSchema($response->content());

        $id = $response->json('id');

        $response = $this->patchToResourceTypeUpdate(
            $id,
            [
                'name' => $this->faker->text(100)
            ]
        );

        $response->assertStatus(204);
    }

    /** @test */
    public function updateBudgetProResourceTypeSuccess(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->postToResourceTypeCreate(
            [
                'name' => $this->faker->text(255),
                'description' => $this->faker->text,
                'item_type_id' => $this->item_types['budget-pro'],
                'public' => false
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonMatchesResourceTypeSchema($response->content());

        $id = $response->json('id');

        $response = $this->patchToResourceTypeUpdate(
            $id,
            [
                'name' => $this->faker->text(100)
            ]
        );

        $response->assertStatus(204);
    }

    /** @test */
    public function updateBudgetResourceTypeSuccess(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->postToResourceTypeCreate(
            [
                'name' => $this->faker->text(255),
                'description' => $this->faker->text,
                'item_type_id' => $this->item_types['budget'],
                'public' => false
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonMatchesResourceTypeSchema($response->content());

        $id = $response->json('id');

        $response = $this->patchToResourceTypeUpdate(
            $id,
            [
                'name' => $this->faker->text(100)
            ]
        );

        $response->assertStatus(204);
    }

    /** @test */
    public function updateGameResourceTypeSuccess(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->postToResourceTypeCreate(
            [
                'name' => $this->faker->text(255),
                'description' => $this->faker->text,
                'item_type_id' => $this->item_types['game'],
                'public' => false
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonMatchesResourceTypeSchema($response->content());

        $id = $response->json('id');

        $response = $this->patchToResourceTypeUpdate(
            $id,
            [
                'name' => $this->faker->text(100)
            ]
        );

        $response->assertStatus(204);
    }
}
