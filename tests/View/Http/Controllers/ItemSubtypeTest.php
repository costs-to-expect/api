<?php

namespace Tests\View\Http\Controllers;

use Tests\TestCase;

final class ItemSubtypeTest extends TestCase
{
    /** @test */
    public function itemSubtypeCollection(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->getToItemSubtypeList(['item_type_id' => $this->item_types['game']]);
        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 4);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesItemSubtypeSchema($json);
        }
    }

    /** @test */
    public function itemSubtypeCollectionPagination(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->getToItemSubtypeList([
            'item_type_id' => $this->item_types['game'],
            'offset' => 0,
            'limit' => 2
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Offset', 0);
        $response->assertHeader('X-Limit', 2);
        $response->assertHeader('X-Count', 2);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesItemSubtypeSchema($json);
        }
    }

    /** @test */
    public function itemSubtypeCollectionSearchName(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->getToItemSubtypeList([
            'item_type_id' => $this->item_types['game'],
            'search' => 'name:yatzy'
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Search', 'name:yatzy');
        $response->assertHeader('X-Count', 1);

        $this->assertEquals('yatzy', $response->json()[0]['name']);
    }

    /** @test */
    public function itemSubtypeCollectionSearchNoResults(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->getToItemSubtypeList([
            'item_type_id' => $this->item_types['game'],
            'search' => 'name:xxxxxxxxx'
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Count', 0);
    }

    /** @test */
    public function itemSubtypeCollectionSortName(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->getToItemSubtypeList([
            'item_type_id' => $this->item_types['game'],
            'sort' => 'name:asc',
            'limit' => 1
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Sort', 'name:asc');
        $this->assertEquals('carcassonne', $response->json()[0]['name']);
    }

    /** @test */
    public function itemSubtypeCollectionInvalidItemType(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->getToItemSubtypeList(['item_type_id' => 'xxxxxxxxxx']);
        $response->assertStatus(403);
    }

    /** @test */
    public function itemSubtypeShow(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->getToItemSubtypeList([
            'item_type_id' => $this->item_types['allocated-expense']
        ]);
        $response->assertStatus(200);

        $item_subtype_id = $response->json()[0]['id'];

        $response = $this->getToItemSubtypeShow([
            'item_type_id' => $this->item_types['allocated-expense'],
            'item_subtype_id' => $item_subtype_id
        ]);
        $response->assertStatus(200);

        $this->assertJsonMatchesItemSubtypeSchema($response->content());
    }

    /** @test */
    public function itemSubtypeShowInvalidId(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->getToItemSubtypeShow([
            'item_type_id' => $this->item_types['allocated-expense'],
            'item_subtype_id' => 'xxxxxxxxxx'
        ]);
        $response->assertStatus(403);
    }

    /** @test */
    public function optionsRequestForItemSubtypeCollection(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->fetchOptionsForItemSubtypeCollection([
            'item_type_id' => $this->item_types['game']
        ]);
        $response->assertStatus(200);
    }

    /** @test */
    public function optionsRequestForItemSubtype(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->getToItemSubtypeList([
            'item_type_id' => $this->item_types['allocated-expense']
        ]);
        $item_subtype_id = $response->json()[0]['id'];

        $response = $this->fetchOptionsForItemSubtype([
            'item_type_id' => $this->item_types['allocated-expense'],
            'item_subtype_id' => $item_subtype_id
        ]);
        $response->assertStatus(200);
    }
}
