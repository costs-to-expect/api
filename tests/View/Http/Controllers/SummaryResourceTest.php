<?php

namespace Tests\View\Http\Controllers;

use Tests\TestCase;

final class SummaryResourceTest extends TestCase
{
    /** @test */
    public function summaryResource(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $response = $this->getToSummaryResourceList(['resource_type_id' => $resource_type_id]);

        $response->assertStatus(200);
        $this->assertEquals(2, $response->json('resources'));
    }

    /** @test */
    public function summaryResourceSearch(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $response = $this->postToResourceCreate($resource_type_id, [
            'name' => 'search-string',
            'description' => $this->faker->text(200),
            'item_subtype_id' => $this->item_subtypes['allocated-expense']['default'],
        ]);
        $response->assertStatus(201);

        $response = $this->getToSummaryResourceList([
            'resource_type_id' => $resource_type_id,
            'search' => 'name:search-string'
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Search', 'name:search-string');
        $this->assertEquals(1, $response->json('resources'));
    }

    /** @test */
    public function optionsRequestForSummaryResourceCollection(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();

        $response = $this->fetchOptionsForSummaryResourceCollection(['resource_type_id' => $resource_type_id]);
        $response->assertStatus(200);
    }
}
