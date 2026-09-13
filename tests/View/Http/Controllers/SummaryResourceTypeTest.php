<?php

namespace Tests\View\Http\Controllers;

use Tests\TestCase;

final class SummaryResourceTypeTest extends TestCase
{
    /** @test */
    public function summaryResourceType(): void
    {
        $this->actingAs($this->createUser());

        $this->quickCreateAllocatedExpenseResourceType();
        $this->quickCreateAllocatedExpenseResourceType();

        $response = $this->getToSummaryResourceTypeList();

        $response->assertStatus(200);
        $this->assertEquals(2, $response->json('resource_types'));
    }

    /** @test */
    public function summaryResourceTypeSearch(): void
    {
        $this->actingAs($this->createUser());

        $this->quickCreateAllocatedExpenseResourceType(['name' => 'search-string']);
        $this->quickCreateAllocatedExpenseResourceType();

        $response = $this->getToSummaryResourceTypeList(['search' => 'name:search-string']);

        $response->assertStatus(200);
        $response->assertHeader('X-Search', 'name:search-string');
        $this->assertEquals(1, $response->json('resource_types'));
    }

    /** @test */
    public function optionsRequestForSummaryResourceTypeCollection(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->fetchOptionsForSummaryResourceTypeCollection();
        $response->assertStatus(200);
    }
}
