<?php

namespace Tests\View\Http\Controllers;

use Tests\TestCase;

final class ItemAllocatedExpenseTest extends TestCase
{
    /** @test */
    public function allocatedExpenseItemCollection(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);
        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);
        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);

        $response = $this->getToItemList([
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

            $this->assertJsonMatchesAllocatedExpenseItemSchema($json);
        }
    }

    /** @test */
    public function allocatedExpenseItemCollectionFilterEffectiveDate(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['effective_date' => '2020-09-12']);
        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['effective_date' => '2020-10-02']);
        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['effective_date' => '2020-10-15']);
        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['effective_date' => '2021-10-15']);

        $response = $this->getToItemList([
            $resource_type_id,
            $resource_id,
            'filter'=>'effective_date:2020-10-01:2020-10-30'
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Filter', 'effective_date:2020-10-01:2020-10-30');
        $response->assertHeader('X-Total-Count', 2);
        $response->assertHeader('X-Count', 2);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesAllocatedExpenseItemSchema($json);
        }
    }

    /** @test */
    public function allocatedExpenseItemCollectionPagination(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);
        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);
        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);

        $response = $this->getToItemList([
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

            $this->assertJsonMatchesAllocatedExpenseItemSchema($json);
        }
    }

    /**
     * @test
     * @dataProvider rogueLimitAndOffsetValues
     */
    public function allocatedExpenseItemCollectionRogueLimitAndOffsetDoesNotError(
        int|string $limit,
        int|string $offset,
        int $expected_limit,
        int $expected_offset
    ): void {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);

        $response = $this->getToItemList([
            $resource_type_id,
            $resource_id,
            'limit' => $limit,
            'offset' => $offset
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Limit', $expected_limit);
        $response->assertHeader('X-Offset', $expected_offset);
    }

    public static function rogueLimitAndOffsetValues(): array
    {
        return [
            'the actual production incident payload' => [-9043281, 0, 10, 0],
            'sqlmap union-based injection payload as limit' => [
                "-9043281' UNION ALL SELECT NULL,NULL,NULL,NULL,CONCAT(0x7e363233667e,(1),0x7e613662627e) -- -",
                0,
                10,
                0
            ],
            'zero limit' => [0, 0, 10, 0],
            'small negative limit' => [-1, 0, 10, 0],
            'negative offset' => [5, -20, 5, 0],
            'negative limit and negative offset together' => [-1, -1, 10, 0],
        ];
    }

    /** @test */
    public function allocatedExpenseItemCollectionSearchDescription(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);
        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['description' => 'search-string']);
        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);

        $response = $this->getToItemList([
            $resource_type_id,
            $resource_id,
            'search'=>'description:search-string'
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Search', 'description:search-string');
        $response->assertHeader('X-Count', 1);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesAllocatedExpenseItemSchema($json);
        }
    }

    /** @test */
    public function allocatedExpenseItemCollectionSearchName(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);
        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['name' => 'search-string']);
        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);

        $response = $this->getToItemList([
            $resource_type_id,
            $resource_id,
            'search'=>'name:search-string'
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Search', 'name:search-string');
        $response->assertHeader('X-Count', 1);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesAllocatedExpenseItemSchema($json);
        }
    }

    /** @test */
    public function allocatedExpenseItemCollectionSortName(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);
        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['name' => 'AAAAAAAAAAAA']);
        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);

        $response = $this->getToItemList([
            $resource_type_id,
            $resource_id,
            'sort'=>'name:asc'
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Sort', 'name:asc');
        $this->assertEquals('AAAAAAAAAAAA', $response->json()[0]['name']);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesAllocatedExpenseItemSchema($json);
        }
    }

    /** @test */
    public function allocatedExpenseItemShow(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);

        $response = $this->getToItemShow([
            $resource_type_id,
            $resource_id,
            $item_id
        ]);
        $response->assertStatus(200);

        $this->assertJsonMatchesAllocatedExpenseItemSchema($response->content());
    }

    /** @test */
    public function optionsRequestForAllocatedExpenseItem(): void
    {
        $this->actingAs($this->createUser());
        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);

        $response = $this->fetchOptionsForItem([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'item_id' => $item_id
        ]);

        $response->assertStatus(200);

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/allocated-expense.json');
    }

    /** @test */
    public function optionsRequestForAllocatedExpenseItemCollection(): void
    {
        $this->actingAs($this->createUser());
        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);
        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);
        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);

        $response = $this->fetchOptionsForItemCollection([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id
        ]);

        $response->assertStatus(200);

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/allocated-expense-collection.json');
    }
}
