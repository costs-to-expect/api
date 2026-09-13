<?php

namespace Tests\View\Http\Controllers;

use Tests\TestCase;

final class SummarySubcategoryTest extends TestCase
{
    /** @test */
    public function summarySubcategory(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $category_id = $this->quickCreateRandomCategory($resource_type_id);
        $this->quickCreateRandomSubcategory($resource_type_id, $category_id);
        $this->quickCreateRandomSubcategory($resource_type_id, $category_id);

        $response = $this->getToSummarySubcategoryList([
            'resource_type_id' => $resource_type_id,
            'category_id' => $category_id
        ]);

        $response->assertStatus(200);
        $this->assertEquals(2, $response->json('subcategories'));
    }

    /** @test */
    public function summarySubcategorySearch(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $category_id = $this->quickCreateRandomCategory($resource_type_id);
        $this->quickCreateRandomSubcategory($resource_type_id, $category_id, ['name' => 'search-string']);
        $this->quickCreateRandomSubcategory($resource_type_id, $category_id);

        $response = $this->getToSummarySubcategoryList([
            'resource_type_id' => $resource_type_id,
            'category_id' => $category_id,
            'search' => 'name:search-string'
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Search', 'name:search-string');
        $this->assertEquals(1, $response->json('subcategories'));
    }

    /** @test */
    public function optionsRequestForSummarySubcategoryCollection(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $category_id = $this->quickCreateRandomCategory($resource_type_id);

        $response = $this->fetchOptionsForSummarySubcategoryCollection([
            'resource_type_id' => $resource_type_id,
            'category_id' => $category_id
        ]);
        $response->assertStatus(200);
    }
}
