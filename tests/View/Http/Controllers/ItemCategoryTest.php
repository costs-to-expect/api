<?php

namespace Tests\View\Http\Controllers;

use Tests\TestCase;

final class ItemCategoryTest extends TestCase
{
    /** @test */
    public function itemCategoryCollectionEmpty(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);

        $response = $this->getToItemCategoryList([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'item_id' => $item_id
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 0);
        $this->assertEquals([], $response->json());
    }

    /** @test */
    public function itemCategoryCollection(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);
        $category_id = $this->quickCreateRandomCategory($resource_type_id);

        $this->quickCreateItemCategory($resource_type_id, $resource_id, $item_id, $category_id);

        $response = $this->getToItemCategoryList([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'item_id' => $item_id
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 1);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesItemCategorySchema($json);
        }
    }

    /** @test */
    public function itemCategoryShow(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);
        $category_id = $this->quickCreateRandomCategory($resource_type_id);

        $item_category_id = $this->quickCreateItemCategory($resource_type_id, $resource_id, $item_id, $category_id);

        $response = $this->getToItemCategoryShow([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'item_id' => $item_id,
            'item_category_id' => $item_category_id
        ]);

        $response->assertStatus(200);
        $this->assertJsonMatchesItemCategorySchema($response->content());
    }

    /** @test */
    public function itemCategoryShowNotFound(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);

        $other_item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);
        $category_id = $this->quickCreateRandomCategory($resource_type_id);
        $other_item_category_id = $this->quickCreateItemCategory($resource_type_id, $resource_id, $other_item_id, $category_id);

        $response = $this->getToItemCategoryShow([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'item_id' => $item_id,
            'item_category_id' => $other_item_category_id
        ]);

        $response->assertStatus(404);
    }

    /** @test */
    public function itemCategoryAssignmentLimitForAllocatedExpense(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);

        $first_category_id = $this->quickCreateRandomCategory($resource_type_id);
        $this->quickCreateItemCategory($resource_type_id, $resource_id, $item_id, $first_category_id);

        $second_category_id = $this->quickCreateRandomCategory($resource_type_id);
        $response = $this->postToItemCategoryCreate(
            $resource_type_id,
            $resource_id,
            $item_id,
            ['category_id' => $second_category_id]
        );

        $response->assertStatus(400);
    }

    /** @test */
    public function itemCategoryGameAllowsMultipleAssignments(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $resource_id = $this->quickCreateYahtzeeResource($resource_type_id);
        $item_id = $this->quickCreateYahtzeeGameItem($resource_type_id, $resource_id);

        $first_category_id = $this->quickCreateRandomCategory($resource_type_id);
        $this->quickCreateItemCategory($resource_type_id, $resource_id, $item_id, $first_category_id);

        $second_category_id = $this->quickCreateRandomCategory($resource_type_id);
        $this->quickCreateItemCategory($resource_type_id, $resource_id, $item_id, $second_category_id);

        $response = $this->getToItemCategoryList([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'item_id' => $item_id
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 2);
    }

    /** @test */
    public function itemCategoryDelete(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);
        $category_id = $this->quickCreateRandomCategory($resource_type_id);

        $item_category_id = $this->quickCreateItemCategory($resource_type_id, $resource_id, $item_id, $category_id);

        $response = $this->deleteToItemCategoryDelete($resource_type_id, $resource_id, $item_id, $item_category_id);
        $response->assertStatus(204);

        $response = $this->getToItemCategoryShow([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'item_id' => $item_id,
            'item_category_id' => $item_category_id
        ]);
        $response->assertStatus(404);
    }

    /** @test */
    public function optionsRequestForItemCategoryCollection(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);

        $response = $this->fetchOptionsForItemCategoryCollection([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'item_id' => $item_id
        ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function optionsRequestForItemCategory(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);
        $category_id = $this->quickCreateRandomCategory($resource_type_id);

        $item_category_id = $this->quickCreateItemCategory($resource_type_id, $resource_id, $item_id, $category_id);

        $response = $this->fetchOptionsForItemCategory([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'item_id' => $item_id,
            'item_category_id' => $item_category_id
        ]);

        $response->assertStatus(200);
    }
}
