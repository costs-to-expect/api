<?php

namespace Tests\Action\Http\Controllers;

use Tests\TestCase;

final class ItemGameTest extends TestCase
{
    /** @test */
    public function createYahtzeeGameItemSuccess(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $resource_id = $this->quickCreateYahtzeeResource($resource_type_id);

        $response = $this->postToItemCreate(
            $resource_type_id,
            $resource_id,
            [
                'name' => $this->faker->text(200),
                'description' => $this->faker->text(200),
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonMatchesGameItemSchema($response->content());
    }

    /** @test */
    public function createYatzyGameItemSuccess(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $resource_id = $this->quickCreateYatzyResource($resource_type_id);

        $response = $this->postToItemCreate(
            $resource_type_id,
            $resource_id,
            [
                'name' => $this->faker->text(200),
                'description' => $this->faker->text(200),
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonMatchesGameItemSchema($response->content());
    }

    /** @test */
    public function deleteYahtzeeGameItemFailsNotFound(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $resource_id = $this->quickCreateYahtzeeResource($resource_type_id);
        $item_id = '1234567890';

        $response = $this->deleteToItemDelete(
            $resource_type_id,
            $resource_id,
            $item_id,
        );

        $response->assertStatus(403);
    }

    /** @test */
    public function deleteYahtzeeGameItemSuccess(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $resource_id = $this->quickCreateYahtzeeResource($resource_type_id);
        $item_id = $this->quickCreateYahtzeeGameItem($resource_type_id, $resource_id);

        $response = $this->deleteToItemDelete(
            $resource_type_id,
            $resource_id,
            $item_id,
        );

        $response->assertStatus(204);
    }

    /** @test */
    public function deleteYatzyGameItemFailsNotFound(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $resource_id = $this->quickCreateYatzyResource($resource_type_id);
        $item_id = '1234567890';

        $response = $this->deleteToItemDelete(
            $resource_type_id,
            $resource_id,
            $item_id,
        );

        $response->assertStatus(403);
    }

    /** @test */
    public function deleteYatzyGameItemSuccess(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $resource_id = $this->quickCreateYatzyResource($resource_type_id);
        $item_id = $this->quickCreateYatzyGameItem($resource_type_id, $resource_id);

        $response = $this->deleteToItemDelete(
            $resource_type_id,
            $resource_id,
            $item_id,
        );

        $response->assertStatus(204);
    }

    /** @test */
    public function updateYahtzeeGameItemFailsNonExistentField(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $resource_id = $this->quickCreateYahtzeeResource($resource_type_id);
        $item_id = $this->quickCreateYahtzeeGameItem($resource_type_id, $resource_id);

        $response = $this->patchToItemUpdate(
            $resource_type_id,
            $resource_id,
            $item_id,
            [
                'i_do_not_exist' => json_encode(['turns'=>[], 'data'=>null], JSON_THROW_ON_ERROR),
            ]
        );

        $response->assertStatus(400);
    }

    /** @test */
    public function updateYahtzeeGameItemFailsNoPayload(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $resource_id = $this->quickCreateYahtzeeResource($resource_type_id);
        $item_id = $this->quickCreateYahtzeeGameItem($resource_type_id, $resource_id);

        $response = $this->patchToItemUpdate(
            $resource_type_id,
            $resource_id,
            $item_id,
            []
        );

        $response->assertStatus(400);
    }

    /** @test */
    public function updateYahtzeeGameItemSuccess(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $resource_id = $this->quickCreateYahtzeeResource($resource_type_id);
        $item_id = $this->quickCreateYahtzeeGameItem($resource_type_id, $resource_id);

        $response = $this->patchToItemUpdate(
            $resource_type_id,
            $resource_id,
            $item_id,
            [
                'game' => json_encode(['turns'=>[], 'data'=>null], JSON_THROW_ON_ERROR),
            ]
        );

        $response->assertStatus(204);
    }

    /** @test */
    public function updateYatzyGameItemFailsNonExistentField(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $resource_id = $this->quickCreateYatzyResource($resource_type_id);
        $item_id = $this->quickCreateYatzyGameItem($resource_type_id, $resource_id);

        $response = $this->patchToItemUpdate(
            $resource_type_id,
            $resource_id,
            $item_id,
            [
                'i_do_not_exist' => json_encode(['turns'=>[], 'data'=>null], JSON_THROW_ON_ERROR),
            ]
        );

        $response->assertStatus(400);
    }

    /** @test */
    public function updateYatzyGameItemFailsNoPayload(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $resource_id = $this->quickCreateYatzyResource($resource_type_id);
        $item_id = $this->quickCreateYatzyGameItem($resource_type_id, $resource_id);

        $response = $this->patchToItemUpdate(
            $resource_type_id,
            $resource_id,
            $item_id,
            []
        );

        $response->assertStatus(400);
    }

    /** @test */
    public function updateYatzyGameItemSuccess(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $resource_id = $this->quickCreateYahtzeeResource($resource_type_id);
        $item_id = $this->quickCreateYahtzeeGameItem($resource_type_id, $resource_id);

        $response = $this->patchToItemUpdate(
            $resource_type_id,
            $resource_id,
            $item_id,
            [
                'game' => json_encode(['turns'=>[], 'data'=>null], JSON_THROW_ON_ERROR),
            ]
        );

        $response->assertStatus(204);
    }
}
