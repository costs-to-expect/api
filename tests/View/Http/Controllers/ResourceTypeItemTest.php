<?php

namespace Tests\View\Http\Controllers;

use Tests\TestCase;

final class ResourceTypeItemTest extends TestCase
{
    /** @test */
    public function resourceTypeItemAllocatedExpenseCollection(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $other_resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);
        $this->quickCreateAllocatedExpenseItem($resource_type_id, $other_resource_id);

        $response = $this->getToResourceTypeItemList(['resource_type_id' => $resource_type_id]);

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 2);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesResourceTypeItemAllocatedExpenseSchema($json);
        }
    }

    /** @test */
    public function resourceTypeItemAllocatedExpenseCollectionFilterYear(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['effective_date' => '2023-05-15']);
        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['effective_date' => '2024-06-20']);
        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['effective_date' => '2024-11-02']);

        $response = $this->getToResourceTypeItemList([
            'resource_type_id' => $resource_type_id,
            'year' => 2024
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 2);

        foreach ($response->json() as $item) {
            $this->assertStringStartsWith('2024', $item['effective_date']);
        }
    }

    /** @test */
    public function resourceTypeItemAllocatedExpenseCollectionFilterYearAndMonth(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['effective_date' => '2024-06-20']);
        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['effective_date' => '2024-11-02']);
        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['effective_date' => '2024-11-18']);

        $response = $this->getToResourceTypeItemList([
            'resource_type_id' => $resource_type_id,
            'year' => 2024,
            'month' => 11
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 2);

        foreach ($response->json() as $item) {
            $this->assertStringStartsWith('2024-11', $item['effective_date']);
        }
    }

    /** @test */
    public function resourceTypeItemAllocatedExpenseCollectionFilterCategory(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);
        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);

        $category_id = $this->quickCreateRandomCategory($resource_type_id);
        $this->quickCreateItemCategory($resource_type_id, $resource_id, $item_id, $category_id);

        $response = $this->getToResourceTypeItemList([
            'resource_type_id' => $resource_type_id,
            'category' => $category_id
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 1);
    }

    /** @test */
    public function resourceTypeItemAllocatedExpenseCollectionIncludeCategories(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);
        $category_id = $this->quickCreateRandomCategory($resource_type_id);
        $this->quickCreateItemCategory($resource_type_id, $resource_id, $item_id, $category_id);

        $response = $this->getToResourceTypeItemList([
            'resource_type_id' => $resource_type_id,
            'include-categories' => 'true'
        ]);

        $response->assertStatus(200);
        $this->assertNotEmpty($response->json()[0]['categories']);
    }

    /** @test */
    public function resourceTypeItemAllocatedExpenseCollectionSortEffectiveDate(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['effective_date' => '2024-06-20']);
        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['effective_date' => '2023-01-01']);

        $response = $this->getToResourceTypeItemList([
            'resource_type_id' => $resource_type_id,
            'sort' => 'effective_date:asc'
        ]);

        $response->assertStatus(200);
        $this->assertEquals('2023-01-01', $response->json()[0]['effective_date']);
    }

    /** @test */
    public function resourceTypeItemAllocatedExpenseCollectionPagination(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);
        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);
        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);

        $response = $this->getToResourceTypeItemList([
            'resource_type_id' => $resource_type_id,
            'offset' => 0,
            'limit' => 2
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Offset', 0);
        $response->assertHeader('X-Limit', 2);
        $response->assertHeader('X-Count', 2);
    }

    /** @test */
    public function resourceTypeItemGameCollection(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $resource_id = $this->quickCreateYahtzeeResource($resource_type_id);
        $this->quickCreateYahtzeeGameItem($resource_type_id, $resource_id);

        $response = $this->getToResourceTypeItemList(['resource_type_id' => $resource_type_id]);

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 1);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesResourceTypeItemGameSchema($json);
        }
    }

    /** @test */
    public function optionsRequestForResourceTypeItemCollection(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();

        $response = $this->fetchOptionsForResourceTypeItemCollection(['resource_type_id' => $resource_type_id]);
        $response->assertStatus(200);
    }
}
