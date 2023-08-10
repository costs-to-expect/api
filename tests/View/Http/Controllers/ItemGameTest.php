<?php

namespace Tests\View\Http\Controllers;

use App\User;
use Tests\TestCase;

final class ItemGameTest extends TestCase
{
    /** @test */
    public function optionsRequestForYahtzeeGameItem(): void
    {
        $this->actingAs(User::find(1));
        $resource_type_id = $this->createGameResourceType();
        $resource_id = $this->createYahtzeeResource($resource_type_id);
        $item_id = $this->createYahtzeeGameItem($resource_type_id, $resource_id);

        $response = $this->fetchOptionsForItem([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'item_id' => $item_id
        ]);

        $response->assertStatus(200);

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/yahtzee.json');
    }

    /** @test */
    public function optionsRequestForYahtzeeGameItemCollection(): void
    {
        $this->actingAs(User::find(1));
        $resource_type_id = $this->createGameResourceType();
        $resource_id = $this->createYahtzeeResource($resource_type_id);

        $this->createYahtzeeGameItem($resource_type_id, $resource_id);
        $this->createYahtzeeGameItem($resource_type_id, $resource_id);
        $this->createYahtzeeGameItem($resource_type_id, $resource_id);

        $response = $this->fetchOptionsForItemCollection([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id
        ]);

        $response->assertStatus(200);

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/yahtzee-collection.json');
    }

    /** @test */
    public function optionsRequestForYatzyGameItem(): void
    {
        $this->actingAs(User::find(1));
        $resource_type_id = $this->createGameResourceType();
        $resource_id = $this->createYatzyResource($resource_type_id);
        $item_id = $this->createYahtzeeGameItem($resource_type_id, $resource_id);

        $response = $this->fetchOptionsForItem([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'item_id' => $item_id
        ]);

        $response->assertStatus(200);

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/yatzy.json');
    }

    /** @test */
    public function optionsRequestForYatzyGameItemCollection(): void
    {
        $this->actingAs(User::find(1));
        $resource_type_id = $this->createGameResourceType();
        $resource_id = $this->createYatzyResource($resource_type_id);

        $this->createYatzyGameItem($resource_type_id, $resource_id);
        $this->createYatzyGameItem($resource_type_id, $resource_id);
        $this->createYatzyGameItem($resource_type_id, $resource_id);

        $response = $this->fetchOptionsForItemCollection([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id
        ]);

        $response->assertStatus(200);

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/yatzy-collection.json');
    }

    /** @test */
    public function yahtzeeGameItemCollection(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createGameResourceType();
        $resource_id = $this->createYahtzeeResource($resource_type_id);

        $this->createYahtzeeGameItem($resource_type_id, $resource_id);
        $this->createYahtzeeGameItem($resource_type_id, $resource_id);
        $this->createYahtzeeGameItem($resource_type_id, $resource_id);

        $response = $this->fetchItemCollection([
            $resource_type_id,
            $resource_id
        ]);

        $response->assertStatus(200);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesGameItemSchema($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function yahtzeeGameItemCollectionPagination(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createGameResourceType();
        $resource_id = $this->createYahtzeeResource($resource_type_id);

        $this->createYahtzeeGameItem($resource_type_id, $resource_id);
        $this->createYahtzeeGameItem($resource_type_id, $resource_id);
        $this->createYahtzeeGameItem($resource_type_id, $resource_id);

        $response = $this->fetchItemCollection([
            $resource_type_id,
            $resource_id,
            'offset'=>0,
            'limit'=> 2
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Offset', 0);
        $response->assertHeader('X-Limit', 2);
        $response->assertHeader('X-Link-Previous', "");
        $response->assertHeader('X-Link-Next', "/v3/resource-types/{$resource_type_id}/resources/{$resource_id}/items?offset=2&limit=2");

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesGameItemSchema($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function yahtzeeGameItemCollectionSortCreated(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createGameResourceType();
        $resource_id = $this->createYahtzeeResource($resource_type_id);

        $this->createYahtzeeGameItem($resource_type_id, $resource_id);
        $this->createYahtzeeGameItem($resource_type_id, $resource_id);
        sleep(1); // ensure the created_at timestamps are different
        $this->createYahtzeeGameItem($resource_type_id, $resource_id, ['name' => 'created-last']);

        $response = $this->fetchItemCollection([
            $resource_type_id,
            $resource_id,
            'sort'=>'created:desc'
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Sort', 'created:desc');
        $this->assertEquals('created-last', $response->json()[0]['name']);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesGameItemSchema($json);
        }
    }

    /** @test */
    public function yahtzeeGameItemShow(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createGameResourceType();
        $resource_id = $this->createYahtzeeResource($resource_type_id);
        $item_id = $this->createYahtzeeGameItem($resource_type_id, $resource_id);

        $response = $this->fetchItem([
            $resource_type_id,
            $resource_id,
            $item_id
        ]);
        $response->assertStatus(200);

        $this->assertJsonMatchesGameItemSchema($response->content());
    }

    /** @test */
    public function yatzyGameItemCollection(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createGameResourceType();
        $resource_id = $this->createYatzyResource($resource_type_id);

        $this->createYatzyGameItem($resource_type_id, $resource_id);
        $this->createYatzyGameItem($resource_type_id, $resource_id);
        $this->createYatzyGameItem($resource_type_id, $resource_id);

        $response = $this->fetchItemCollection([
            $resource_type_id,
            $resource_id
        ]);

        $response->assertStatus(200);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesGameItemSchema($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function yatzyGameItemCollectionPagination(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createGameResourceType();
        $resource_id = $this->createYatzyResource($resource_type_id);

        $this->createYatzyGameItem($resource_type_id, $resource_id);
        $this->createYatzyGameItem($resource_type_id, $resource_id);
        $this->createYatzyGameItem($resource_type_id, $resource_id);

        $response = $this->fetchItemCollection([
            $resource_type_id,
            $resource_id,
            'offset'=>0,
            'limit'=> 2
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Offset', 0);
        $response->assertHeader('X-Limit', 2);
        $response->assertHeader('X-Link-Previous', "");
        $response->assertHeader('X-Link-Next', "/v3/resource-types/{$resource_type_id}/resources/{$resource_id}/items?offset=2&limit=2");

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesGameItemSchema($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function yatzyGameItemCollectionSortCreated(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createGameResourceType();
        $resource_id = $this->createYatzyResource($resource_type_id);

        $this->createYatzyGameItem($resource_type_id, $resource_id);
        $this->createYatzyGameItem($resource_type_id, $resource_id);
        sleep(1); // ensure the created_at timestamps are different
        $this->createYatzyGameItem($resource_type_id, $resource_id, ['name' => 'created-last']);

        $response = $this->fetchItemCollection([
            $resource_type_id,
            $resource_id,
            'sort'=>'created:desc'
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Sort', 'created:desc');
        $this->assertEquals('created-last', $response->json()[0]['name']);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesGameItemSchema($json);
        }
    }

    /** @test */
    public function yatzyGameItemShow(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createGameResourceType();
        $resource_id = $this->createYatzyResource($resource_type_id);
        $item_id = $this->createYatzyGameItem($resource_type_id, $resource_id);

        $response = $this->fetchItem([
            $resource_type_id,
            $resource_id,
            $item_id
        ]);
        $response->assertStatus(200);

        $this->assertJsonMatchesGameItemSchema($response->content());
    }
}
