<?php

namespace Tests\View\Http\Controllers;

use Tests\TestCase;

final class SummaryResourceTypeItemTest extends TestCase
{
    /** @test */
    public function summaryResourceTypeItem(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['total' => '100.00']);
        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['total' => '50.00']);

        $response = $this->getToSummaryResourceTypeItemList(['resource_type_id' => $resource_type_id]);

        $response->assertStatus(200);
        $this->assertEquals('GBP', $response->json()[0]['currency']['code']);
        $this->assertEquals(2, $response->json()[0]['count']);
        $this->assertEquals('150.00', $response->json()[0]['subtotal']);
    }

    /** @test */
    public function summaryResourceTypeItemByYears(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['total' => '100.00', 'effective_date' => '2023-06-01']);
        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['total' => '50.00', 'effective_date' => '2024-06-01']);

        $response = $this->getToSummaryResourceTypeItemList([
            'resource_type_id' => $resource_type_id,
            'years' => 'true'
        ]);

        $response->assertStatus(200);
        $this->assertCount(2, $response->json());
        $this->assertEquals(2023, $response->json()[0]['year']);
        $this->assertEquals('100.00', $response->json()[0]['subtotals'][0]['subtotal']);
        $this->assertEquals(2024, $response->json()[1]['year']);
        $this->assertEquals('50.00', $response->json()[1]['subtotals'][0]['subtotal']);
    }

    /** @test */
    public function summaryResourceTypeItemByYear(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['total' => '100.00', 'effective_date' => '2023-06-01']);
        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['total' => '50.00', 'effective_date' => '2024-06-01']);

        $response = $this->getToSummaryResourceTypeItemList([
            'resource_type_id' => $resource_type_id,
            'year' => 2024
        ]);

        $response->assertStatus(200);
        $this->assertEquals(2024, $response->json('year'));
        $this->assertEquals('50.00', $response->json('subtotals.0.subtotal'));
    }

    /** @test */
    public function summaryResourceTypeItemByMonths(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['total' => '100.00', 'effective_date' => '2024-06-15']);
        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['total' => '50.00', 'effective_date' => '2024-11-02']);

        $response = $this->getToSummaryResourceTypeItemList([
            'resource_type_id' => $resource_type_id,
            'year' => 2024,
            'months' => 'true'
        ]);

        $response->assertStatus(200);
        $this->assertCount(2, $response->json());
        $this->assertEquals('June', $response->json()[0]['month']);
        $this->assertEquals('November', $response->json()[1]['month']);
    }

    /** @test */
    public function summaryResourceTypeItemByMonth(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['total' => '100.00', 'effective_date' => '2024-06-15']);
        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['total' => '25.00', 'effective_date' => '2024-06-20']);
        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['total' => '50.00', 'effective_date' => '2024-11-02']);

        $response = $this->getToSummaryResourceTypeItemList([
            'resource_type_id' => $resource_type_id,
            'year' => 2024,
            'month' => 6
        ]);

        $response->assertStatus(200);
        $this->assertEquals('June', $response->json('month'));
        $this->assertEquals(2, $response->json('subtotals.0.count'));
        $this->assertEquals('125.00', $response->json('subtotals.0.subtotal'));
    }

    /** @test */
    public function summaryResourceTypeItemByCategories(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['total' => '100.00']);
        $category_id = $this->quickCreateRandomCategory($resource_type_id);
        $this->quickCreateItemCategory($resource_type_id, $resource_id, $item_id, $category_id);

        $response = $this->getToSummaryResourceTypeItemList([
            'resource_type_id' => $resource_type_id,
            'categories' => 'true'
        ]);

        $response->assertStatus(200);
        $this->assertCount(1, $response->json());
        $this->assertEquals('100.00', $response->json()[0]['subtotals'][0]['subtotal']);
    }

    /** @test */
    public function summaryResourceTypeItemByCategory(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['total' => '100.00']);
        $category_id = $this->quickCreateRandomCategory($resource_type_id);
        $this->quickCreateItemCategory($resource_type_id, $resource_id, $item_id, $category_id);

        $response = $this->getToSummaryResourceTypeItemList([
            'resource_type_id' => $resource_type_id,
            'category' => $category_id
        ]);

        $response->assertStatus(200);
        $this->assertEquals('100.00', $response->json('subtotals.0.subtotal'));
    }

    /** @test */
    public function summaryResourceTypeItemByResources(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $other_resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['total' => '100.00']);
        $this->quickCreateAllocatedExpenseItem($resource_type_id, $other_resource_id, ['total' => '50.00']);

        $response = $this->getToSummaryResourceTypeItemList([
            'resource_type_id' => $resource_type_id,
            'resources' => 'true'
        ]);

        $response->assertStatus(200);
        $this->assertCount(2, $response->json());
    }

    /** @test */
    public function optionsRequestForSummaryResourceTypeItemCollection(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();

        $response = $this->fetchOptionsForSummaryResourceTypeItemCollection(['resource_type_id' => $resource_type_id]);
        $response->assertStatus(200);
    }
}
