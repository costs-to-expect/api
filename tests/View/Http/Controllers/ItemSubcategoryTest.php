<?php

namespace Tests\View\Http\Controllers;

use Tests\TestCase;

final class ItemSubcategoryTest extends TestCase
{
    /** @test */
    public function itemSubcategoryCollectionEmpty(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);
        $category_id = $this->quickCreateRandomCategory($resource_type_id);
        $item_category_id = $this->quickCreateItemCategory($resource_type_id, $resource_id, $item_id, $category_id);

        $response = $this->getToItemSubcategoryList([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'item_id' => $item_id,
            'item_category_id' => $item_category_id
        ]);

        $response->assertStatus(200);
        $this->assertEquals([], $response->json());
    }

    /** @test */
    public function itemSubcategoryCollection(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);
        $category_id = $this->quickCreateRandomCategory($resource_type_id);
        $item_category_id = $this->quickCreateItemCategory($resource_type_id, $resource_id, $item_id, $category_id);
        $subcategory_id = $this->quickCreateRandomSubcategory($resource_type_id, $category_id);

        $this->quickCreateItemSubcategory($resource_type_id, $resource_id, $item_id, $item_category_id, $subcategory_id);

        $response = $this->getToItemSubcategoryList([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'item_id' => $item_id,
            'item_category_id' => $item_category_id
        ]);

        $response->assertStatus(200);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesItemSubcategorySchema($json);
        }
    }

    /** @test */
    public function itemSubcategoryShow(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);
        $category_id = $this->quickCreateRandomCategory($resource_type_id);
        $item_category_id = $this->quickCreateItemCategory($resource_type_id, $resource_id, $item_id, $category_id);
        $subcategory_id = $this->quickCreateRandomSubcategory($resource_type_id, $category_id);

        $item_subcategory_id = $this->quickCreateItemSubcategory($resource_type_id, $resource_id, $item_id, $item_category_id, $subcategory_id);

        $response = $this->getToItemSubcategoryShow([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'item_id' => $item_id,
            'item_category_id' => $item_category_id,
            'item_subcategory_id' => $item_subcategory_id
        ]);

        $response->assertStatus(200);
        $this->assertJsonMatchesItemSubcategorySchema($response->content());
    }

    /** @test */
    public function itemSubcategoryShowNotFound(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);
        $category_id = $this->quickCreateRandomCategory($resource_type_id);
        $item_category_id = $this->quickCreateItemCategory($resource_type_id, $resource_id, $item_id, $category_id);

        $other_item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);
        $other_category_id = $this->quickCreateRandomCategory($resource_type_id);
        $other_item_category_id = $this->quickCreateItemCategory($resource_type_id, $resource_id, $other_item_id, $other_category_id);
        $other_subcategory_id = $this->quickCreateRandomSubcategory($resource_type_id, $other_category_id);
        $other_item_subcategory_id = $this->quickCreateItemSubcategory($resource_type_id, $resource_id, $other_item_id, $other_item_category_id, $other_subcategory_id);

        $response = $this->getToItemSubcategoryShow([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'item_id' => $item_id,
            'item_category_id' => $item_category_id,
            'item_subcategory_id' => $other_item_subcategory_id
        ]);

        $response->assertStatus(404);
    }

    /** @test */
    public function itemSubcategoryAssignmentLimit(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);
        $category_id = $this->quickCreateRandomCategory($resource_type_id);
        $item_category_id = $this->quickCreateItemCategory($resource_type_id, $resource_id, $item_id, $category_id);

        $first_subcategory_id = $this->quickCreateRandomSubcategory($resource_type_id, $category_id);
        $this->quickCreateItemSubcategory($resource_type_id, $resource_id, $item_id, $item_category_id, $first_subcategory_id);

        $second_subcategory_id = $this->quickCreateRandomSubcategory($resource_type_id, $category_id);
        $response = $this->postToItemSubcategoryCreate(
            $resource_type_id,
            $resource_id,
            $item_id,
            $item_category_id,
            ['subcategory_id' => $second_subcategory_id]
        );

        $response->assertStatus(400);
    }

    /** @test */
    public function itemSubcategoryNotSupportedForGame(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateGameResourceType();
        $resource_id = $this->quickCreateYahtzeeResource($resource_type_id);
        $item_id = $this->quickCreateYahtzeeGameItem($resource_type_id, $resource_id);
        $category_id = $this->quickCreateRandomCategory($resource_type_id);
        $item_category_id = $this->quickCreateItemCategory($resource_type_id, $resource_id, $item_id, $category_id);
        $subcategory_id = $this->quickCreateRandomSubcategory($resource_type_id, $category_id);

        $response = $this->postToItemSubcategoryCreate(
            $resource_type_id,
            $resource_id,
            $item_id,
            $item_category_id,
            ['subcategory_id' => $subcategory_id]
        );

        $response->assertStatus(400);
    }

    /** @test */
    public function itemSubcategoryDelete(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);
        $category_id = $this->quickCreateRandomCategory($resource_type_id);
        $item_category_id = $this->quickCreateItemCategory($resource_type_id, $resource_id, $item_id, $category_id);
        $subcategory_id = $this->quickCreateRandomSubcategory($resource_type_id, $category_id);

        $item_subcategory_id = $this->quickCreateItemSubcategory($resource_type_id, $resource_id, $item_id, $item_category_id, $subcategory_id);

        $response = $this->deleteToItemSubcategoryDelete($resource_type_id, $resource_id, $item_id, $item_category_id, $item_subcategory_id);
        $response->assertStatus(204);

        $response = $this->getToItemSubcategoryShow([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'item_id' => $item_id,
            'item_category_id' => $item_category_id,
            'item_subcategory_id' => $item_subcategory_id
        ]);
        $response->assertStatus(404);
    }

    /** @test */
    public function optionsRequestForItemSubcategoryCollection(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);
        $category_id = $this->quickCreateRandomCategory($resource_type_id);
        $item_category_id = $this->quickCreateItemCategory($resource_type_id, $resource_id, $item_id, $category_id);

        $response = $this->fetchOptionsForItemSubcategoryCollection([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'item_id' => $item_id,
            'item_category_id' => $item_category_id
        ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function optionsRequestForItemSubcategory(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);
        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id);
        $category_id = $this->quickCreateRandomCategory($resource_type_id);
        $item_category_id = $this->quickCreateItemCategory($resource_type_id, $resource_id, $item_id, $category_id);
        $subcategory_id = $this->quickCreateRandomSubcategory($resource_type_id, $category_id);

        $item_subcategory_id = $this->quickCreateItemSubcategory($resource_type_id, $resource_id, $item_id, $item_category_id, $subcategory_id);

        $response = $this->fetchOptionsForItemSubcategory([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'item_id' => $item_id,
            'item_category_id' => $item_category_id,
            'item_subcategory_id' => $item_subcategory_id
        ]);

        $response->assertStatus(200);
    }
}
