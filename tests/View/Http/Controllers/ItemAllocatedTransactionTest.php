<?php

namespace Tests\View\Http\Controllers;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ItemAllocatedTransactionTest extends TestCase
{
    #[Test]
    public function allocatedTransactionItemCollection(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedTransactionResourceType();
        $resource_id = $this->quickCreateAllocatedTransactionResource($resource_type_id);

        $this->quickCreateAllocatedTransactionItem($resource_type_id, $resource_id);
        $this->quickCreateAllocatedTransactionItem($resource_type_id, $resource_id);
        $this->quickCreateAllocatedTransactionItem($resource_type_id, $resource_id);

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

            $this->assertJsonMatchesAllocatedTransactionItemSchema($json);
        }
    }

    #[Test]
    public function allocatedTransactionItemCollectionFilterEffectiveDate(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedTransactionResourceType();
        $resource_id = $this->quickCreateAllocatedTransactionResource($resource_type_id);

        $this->quickCreateAllocatedTransactionItem($resource_type_id, $resource_id, ['effective_date' => '2020-09-12']);
        $this->quickCreateAllocatedTransactionItem($resource_type_id, $resource_id, ['effective_date' => '2020-10-02']);
        $this->quickCreateAllocatedTransactionItem($resource_type_id, $resource_id, ['effective_date' => '2020-10-15']);
        $this->quickCreateAllocatedTransactionItem($resource_type_id, $resource_id, ['effective_date' => '2021-10-15']);

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

            $this->assertJsonMatchesAllocatedTransactionItemSchema($json);
        }
    }

    #[Test]
    public function allocatedTransactionItemCollectionPagination(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedTransactionResourceType();
        $resource_id = $this->quickCreateAllocatedTransactionResource($resource_type_id);

        $this->quickCreateAllocatedTransactionItem($resource_type_id, $resource_id);
        $this->quickCreateAllocatedTransactionItem($resource_type_id, $resource_id);
        $this->quickCreateAllocatedTransactionItem($resource_type_id, $resource_id);

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

            $this->assertJsonMatchesAllocatedTransactionItemSchema($json);
        }
    }

    #[Test]
    #[DataProvider('rogueLimitAndOffsetValues')]
    public function allocatedTransactionItemCollectionRogueLimitAndOffsetDoesNotError(
        int|string $limit,
        int|string $offset,
        int $expected_limit,
        int $expected_offset
    ): void {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedTransactionResourceType();
        $resource_id = $this->quickCreateAllocatedTransactionResource($resource_type_id);

        $this->quickCreateAllocatedTransactionItem($resource_type_id, $resource_id);

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

    #[Test]
    public function allocatedTransactionItemCollectionSearchDescription(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedTransactionResourceType();
        $resource_id = $this->quickCreateAllocatedTransactionResource($resource_type_id);

        $this->quickCreateAllocatedTransactionItem($resource_type_id, $resource_id);
        $this->quickCreateAllocatedTransactionItem($resource_type_id, $resource_id, ['description' => 'search-string']);
        $this->quickCreateAllocatedTransactionItem($resource_type_id, $resource_id);

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

            $this->assertJsonMatchesAllocatedTransactionItemSchema($json);
        }
    }

    #[Test]
    public function allocatedTransactionItemCollectionSearchName(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedTransactionResourceType();
        $resource_id = $this->quickCreateAllocatedTransactionResource($resource_type_id);

        $this->quickCreateAllocatedTransactionItem($resource_type_id, $resource_id);
        $this->quickCreateAllocatedTransactionItem($resource_type_id, $resource_id, ['name' => 'search-string']);
        $this->quickCreateAllocatedTransactionItem($resource_type_id, $resource_id);

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

            $this->assertJsonMatchesAllocatedTransactionItemSchema($json);
        }
    }

    #[Test]
    public function allocatedTransactionItemCollectionSortName(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedTransactionResourceType();
        $resource_id = $this->quickCreateAllocatedTransactionResource($resource_type_id);

        $this->quickCreateAllocatedTransactionItem($resource_type_id, $resource_id, ['name' => 'MMMMMMMMMMMM']);
        $this->quickCreateAllocatedTransactionItem($resource_type_id, $resource_id, ['name' => 'AAAAAAAAAAAA']);
        $this->quickCreateAllocatedTransactionItem($resource_type_id, $resource_id, ['name' => 'ZZZZZZZZZZZZ']);

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

            $this->assertJsonMatchesAllocatedTransactionItemSchema($json);
        }
    }

    #[Test]
    public function allocatedTransactionItemCollectionSortTransactionType(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedTransactionResourceType();
        $resource_id = $this->quickCreateAllocatedTransactionResource($resource_type_id);

        $this->quickCreateAllocatedTransactionItem($resource_type_id, $resource_id, ['transaction_type' => 'income']);
        $this->quickCreateAllocatedTransactionItem($resource_type_id, $resource_id, ['transaction_type' => 'expense']);

        $response = $this->getToItemList([
            $resource_type_id,
            $resource_id,
            'sort'=>'transaction_type:asc'
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Sort', 'transaction_type:asc');
        $this->assertEquals('expense', $response->json()[0]['transaction_type']);
        $this->assertEquals('income', $response->json()[1]['transaction_type']);
    }

    #[Test]
    public function allocatedTransactionItemCollectionFilterByTransactionType(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedTransactionResourceType();
        $resource_id = $this->quickCreateAllocatedTransactionResource($resource_type_id);

        $this->quickCreateAllocatedTransactionItem($resource_type_id, $resource_id, ['transaction_type' => 'income']);
        $this->quickCreateAllocatedTransactionItem($resource_type_id, $resource_id, ['transaction_type' => 'expense']);
        $this->quickCreateAllocatedTransactionItem($resource_type_id, $resource_id, ['transaction_type' => 'expense']);

        $response = $this->getToItemList([
            $resource_type_id,
            $resource_id,
            'transaction_type' => 'expense'
        ]);

        $response->assertStatus(200);
        $this->assertCount(2, $response->json());

        foreach ($response->json() as $item) {
            $this->assertEquals('expense', $item['transaction_type']);

            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesAllocatedTransactionItemSchema($json);
        }
    }

    #[Test]
    public function allocatedTransactionItemCollectionInvalidTransactionTypeFilterIsIgnored(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedTransactionResourceType();
        $resource_id = $this->quickCreateAllocatedTransactionResource($resource_type_id);

        $this->quickCreateAllocatedTransactionItem($resource_type_id, $resource_id, ['transaction_type' => 'income']);
        $this->quickCreateAllocatedTransactionItem($resource_type_id, $resource_id, ['transaction_type' => 'expense']);

        $response = $this->getToItemList([
            $resource_type_id,
            $resource_id,
            'transaction_type' => 'not-a-real-type'
        ]);

        $response->assertStatus(200);
        $this->assertCount(2, $response->json());
    }

    #[Test]
    public function allocatedTransactionItemShow(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedTransactionResourceType();
        $resource_id = $this->quickCreateAllocatedTransactionResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedTransactionItem($resource_type_id, $resource_id, ['transaction_type' => 'income']);

        $response = $this->getToItemShow([
            $resource_type_id,
            $resource_id,
            $item_id
        ]);
        $response->assertStatus(200);

        $this->assertEquals('income', $response->json('transaction_type'));
        $this->assertJsonMatchesAllocatedTransactionItemSchema($response->content());
    }

    #[Test]
    public function allocatedTransactionItemCreateRequiresTransactionType(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedTransactionResourceType();
        $resource_id = $this->quickCreateAllocatedTransactionResource($resource_type_id);

        $response = $this->postToItemCreate($resource_type_id, $resource_id, [
            'name' => $this->faker->text(200),
            'description' => $this->faker->text(200),
            'effective_date' => $this->faker->date(),
            'currency_id' => $this->currency['GBP'],
            'total' => $this->randomMoneyValue(),
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'fields' => ['transaction_type']]);
    }

    #[Test]
    public function allocatedTransactionItemCreateRejectsInvalidTransactionType(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedTransactionResourceType();
        $resource_id = $this->quickCreateAllocatedTransactionResource($resource_type_id);

        $response = $this->postToItemCreate($resource_type_id, $resource_id, [
            'name' => $this->faker->text(200),
            'description' => $this->faker->text(200),
            'effective_date' => $this->faker->date(),
            'currency_id' => $this->currency['GBP'],
            'total' => $this->randomMoneyValue(),
            'transaction_type' => 'not-a-real-type',
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'fields' => ['transaction_type']]);
    }

    #[Test]
    public function allocatedTransactionItemUpdateTransactionType(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedTransactionResourceType();
        $resource_id = $this->quickCreateAllocatedTransactionResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedTransactionItem($resource_type_id, $resource_id, ['transaction_type' => 'expense']);

        $response = $this->patchToItemUpdate($resource_type_id, $resource_id, $item_id, [
            'transaction_type' => 'income'
        ]);

        $response->assertStatus(204);

        $show_response = $this->getToItemShow([$resource_type_id, $resource_id, $item_id]);
        $show_response->assertStatus(200);
        $this->assertEquals('income', $show_response->json('transaction_type'));
    }

    #[Test]
    public function allocatedTransactionItemUpdateRejectsInvalidTransactionType(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedTransactionResourceType();
        $resource_id = $this->quickCreateAllocatedTransactionResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedTransactionItem($resource_type_id, $resource_id);

        $response = $this->patchToItemUpdate($resource_type_id, $resource_id, $item_id, [
            'transaction_type' => 'not-a-real-type'
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'fields' => ['transaction_type']]);
    }

    #[Test]
    public function optionsRequestForAllocatedTransactionItem(): void
    {
        $this->actingAs($this->createUser());
        $resource_type_id = $this->quickCreateAllocatedTransactionResourceType();
        $resource_id = $this->quickCreateAllocatedTransactionResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedTransactionItem($resource_type_id, $resource_id);

        $response = $this->fetchOptionsForItem([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'item_id' => $item_id
        ]);

        $response->assertStatus(200);

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/allocated-transaction.json');
    }

    #[Test]
    public function optionsRequestForAllocatedTransactionItemCollection(): void
    {
        $this->actingAs($this->createUser());
        $resource_type_id = $this->quickCreateAllocatedTransactionResourceType();
        $resource_id = $this->quickCreateAllocatedTransactionResource($resource_type_id);

        $this->quickCreateAllocatedTransactionItem($resource_type_id, $resource_id);
        $this->quickCreateAllocatedTransactionItem($resource_type_id, $resource_id);
        $this->quickCreateAllocatedTransactionItem($resource_type_id, $resource_id);

        $response = $this->fetchOptionsForItemCollection([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id
        ]);

        $response->assertStatus(200);

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/allocated-transaction-collection.json');
    }
}
