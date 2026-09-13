<?php

namespace Tests\View\Http\Controllers;

use Tests\TestCase;

final class SummaryCategoryTest extends TestCase
{
    /** @test */
    public function summaryCategory(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $this->quickCreateRandomCategory($resource_type_id);
        $this->quickCreateRandomCategory($resource_type_id);

        $response = $this->getToSummaryCategoryList(['resource_type_id' => $resource_type_id]);

        $response->assertStatus(200);
        $this->assertEquals(2, $response->json('categories'));
    }

    /** @test */
    public function summaryCategorySearch(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $this->quickCreateRandomCategory($resource_type_id, ['name' => 'search-string']);
        $this->quickCreateRandomCategory($resource_type_id);

        $response = $this->getToSummaryCategoryList([
            'resource_type_id' => $resource_type_id,
            'search' => 'name:search-string'
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Search', 'name:search-string');
        $this->assertEquals(1, $response->json('categories'));
    }

    /** @test */
    public function optionsRequestForSummaryCategoryCollection(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();

        $response = $this->fetchOptionsForSummaryCategoryCollection(['resource_type_id' => $resource_type_id]);
        $response->assertStatus(200);
    }
}
