<?php

namespace Tests\View\Http\Controllers;

use Tests\TestCase;

final class ItemTypeTest extends TestCase
{
    /** @test */
    public function itemTypeCollection(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->getToItemTypeList();
        $response->assertStatus(200);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesItemTypeSchema($json);
        }
    }

    /** @test */
    public function itemTypeCollectionPagination(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->getToItemTypeList(['offset'=>1, 'limit'=> 2]);

        $response->assertStatus(200);
        $response->assertHeader('X-Offset', 1);
        $response->assertHeader('X-Limit', 2);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesItemTypeSchema($json);
        }
    }

    /** @test */
    public function itemTypeCollectionSearchDescription(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->getToItemTypeList(['search'=>'description:track']);

        $response->assertStatus(200);
        $response->assertHeader('X-Search', 'description:track');
        $response->assertHeader('X-Count', 2);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesItemTypeSchema($json);
        }
    }

    /** @test */
    public function itemTypeCollectionSearchName(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->getToItemTypeList(['search'=>'name:game']);

        $response->assertStatus(200);
        $response->assertHeader('X-Search', 'name:game');
        $response->assertHeader('X-Count', 1);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesItemTypeSchema($json);
        }
    }

    /** @test */
    public function itemTypeCollectionSearchNameNoResults(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->getToItemTypeList(['search'=>'name:xxxxxxxxx']);

        $response->assertStatus(200);
        $response->assertHeader('X-Search', 'name:xxxxxxxxx');
        $response->assertHeader('X-Count', 0);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesItemTypeSchema($json);
        }
    }

    /** @test */
    public function itemTypeCollectionSortName(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->getToItemTypeList(['sort'=>'name:asc', 'limit' => 1]);

        $response->assertStatus(200);
        $response->assertHeader('X-Sort', 'name:asc');
        $response->assertHeader('X-Count', 1);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesItemTypeSchema($json);
        }

        $this->assertEquals('allocated-expense', $response->json()[0]['name']);

    }

    /** @test */
    public function itemTypeShow(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->getToItemTypeList(['offset'=>0, 'limit'=> 1]);
        $response->assertStatus(200);

        $item_type_id = $response->json()[0]['id'];

        $response = $this->fetchItemType(['item_type_id'=> $item_type_id]);
        $response->assertStatus(200);

        $this->assertJsonMatchesItemTypeSchema($response->content());
    }
}
