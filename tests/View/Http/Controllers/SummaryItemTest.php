<?php

namespace Tests\View\Http\Controllers;

use Tests\TestCase;

final class SummaryItemTest extends TestCase
{
    /** @test */
    public function summaryItem(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['total' => '100.00']);
        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['total' => '50.00']);

        $response = $this->getToSummaryItemList([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id
        ]);

        $response->assertStatus(200);
        $this->assertEquals('GBP', $response->json()[0]['currency']['code']);
        $this->assertEquals(2, $response->json()[0]['count']);
        $this->assertEquals('150.00', $response->json()[0]['subtotal']);
    }

    /** @test */
    public function summaryItemByYears(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['total' => '100.00', 'effective_date' => '2023-06-01']);
        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['total' => '50.00', 'effective_date' => '2024-06-01']);

        $response = $this->getToSummaryItemList([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'years' => 'true'
        ]);

        $response->assertStatus(200);
        $this->assertCount(2, $response->json());
    }

    /** @test */
    public function summaryItemByYearAndMonth(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['total' => '100.00', 'effective_date' => '2024-06-15']);
        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['total' => '25.00', 'effective_date' => '2024-06-20']);
        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['total' => '50.00', 'effective_date' => '2024-11-02']);

        $response = $this->getToSummaryItemList([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'year' => 2024,
            'month' => 6
        ]);

        $response->assertStatus(200);
        $this->assertEquals('June', $response->json('month'));
        $this->assertEquals(2, $response->json('subtotals.0.count'));
        $this->assertEquals('125.00', $response->json('subtotals.0.subtotal'));
    }

    /** @test */
    public function summaryItemByCategories(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['total' => '100.00']);
        $category_id = $this->quickCreateRandomCategory($resource_type_id);
        $this->quickCreateItemCategory($resource_type_id, $resource_id, $item_id, $category_id);

        $response = $this->getToSummaryItemList([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'categories' => 'true'
        ]);

        $response->assertStatus(200);
        $this->assertCount(1, $response->json());
        $this->assertEquals('100.00', $response->json()[0]['subtotals'][0]['subtotal']);
    }

    /** @test */
    public function summaryItemByYear(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['total' => '100.00', 'effective_date' => '2024-03-01']);
        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['total' => '50.00', 'effective_date' => '2024-09-01']);
        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['total' => '25.00', 'effective_date' => '2023-01-01']);

        $response = $this->getToSummaryItemList([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'year' => 2024
        ]);

        $response->assertStatus(200);
        $this->assertEquals(2024, $response->json('year'));
        $this->assertEquals(2, $response->json('subtotals.0.count'));
        $this->assertEquals('150.00', $response->json('subtotals.0.subtotal'));
    }

    /** @test */
    public function summaryItemByYearAndMonths(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['total' => '100.00', 'effective_date' => '2024-06-15']);
        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['total' => '25.00', 'effective_date' => '2024-06-20']);
        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['total' => '50.00', 'effective_date' => '2024-11-02']);

        $response = $this->getToSummaryItemList([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'year' => 2024,
            'months' => 'true'
        ]);

        $response->assertStatus(200);
        $this->assertCount(2, $response->json());
    }

    /** @test */
    public function summaryItemByCategory(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['total' => '100.00']);
        $category_id = $this->quickCreateRandomCategory($resource_type_id);
        $this->quickCreateItemCategory($resource_type_id, $resource_id, $item_id, $category_id);

        $response = $this->getToSummaryItemList([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'category' => $category_id
        ]);

        $response->assertStatus(200);
        $this->assertEquals($category_id, $response->json('id'));
        $this->assertEquals('100.00', $response->json('subtotals.0.subtotal'));
    }

    /** @test */
    public function summaryItemByCategoryAndSubcategories(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['total' => '100.00']);
        $category_id = $this->quickCreateRandomCategory($resource_type_id);
        $item_category_id = $this->quickCreateItemCategory($resource_type_id, $resource_id, $item_id, $category_id);
        $subcategory_id = $this->quickCreateRandomSubcategory($resource_type_id, $category_id);
        $this->quickCreateItemSubcategory($resource_type_id, $resource_id, $item_id, $item_category_id, $subcategory_id);

        $response = $this->getToSummaryItemList([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'category' => $category_id,
            'subcategories' => 'true'
        ]);

        $response->assertStatus(200);
        $this->assertCount(1, $response->json());
        $this->assertEquals($subcategory_id, $response->json('0.id'));
        $this->assertEquals('100.00', $response->json('0.subtotals.0.subtotal'));
    }

    /** @test */
    public function summaryItemByCategoryAndSubcategory(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['total' => '100.00']);
        $category_id = $this->quickCreateRandomCategory($resource_type_id);
        $item_category_id = $this->quickCreateItemCategory($resource_type_id, $resource_id, $item_id, $category_id);
        $subcategory_id = $this->quickCreateRandomSubcategory($resource_type_id, $category_id);
        $this->quickCreateItemSubcategory($resource_type_id, $resource_id, $item_id, $item_category_id, $subcategory_id);

        $response = $this->getToSummaryItemList([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'category' => $category_id,
            'subcategory' => $subcategory_id
        ]);

        $response->assertStatus(200);
        $this->assertEquals($subcategory_id, $response->json('id'));
        $this->assertEquals('100.00', $response->json('subtotals.0.subtotal'));
    }

    /** @test */
    public function summaryItemFilteredBySearch(): void
    {
        // filteredSummary() inner-joins item_category/item_sub_category, so an
        // item only surfaces here once it has both a category and a subcategory
        // assigned, even when filtering purely by a text search.
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $category_id = $this->quickCreateRandomCategory($resource_type_id);
        $subcategory_id = $this->quickCreateRandomSubcategory($resource_type_id, $category_id);

        $other_item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['total' => '100.00']);
        $other_item_category_id = $this->quickCreateItemCategory($resource_type_id, $resource_id, $other_item_id, $category_id);
        $this->quickCreateItemSubcategory($resource_type_id, $resource_id, $other_item_id, $other_item_category_id, $subcategory_id);

        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['total' => '25.00', 'name' => 'search-string']);
        $item_category_id = $this->quickCreateItemCategory($resource_type_id, $resource_id, $item_id, $category_id);
        $this->quickCreateItemSubcategory($resource_type_id, $resource_id, $item_id, $item_category_id, $subcategory_id);

        $response = $this->getToSummaryItemList([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'search' => 'name:search-string'
        ]);

        $response->assertStatus(200);
        $this->assertEquals(1, $response->json('0.count'));
        $this->assertEquals('25.00', $response->json('0.subtotal'));
    }

    /** @test */
    public function optionsRequestForSummaryItemCollection(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $response = $this->fetchOptionsForSummaryItemCollection([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id
        ]);
        $response->assertStatus(200);
    }
}
