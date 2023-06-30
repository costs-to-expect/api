<?php

namespace Tests\View\Http\Controllers;

use App\User;
use Tests\TestCase;

final class SubcategoryTest extends TestCase
{
    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function allocatedExpenseSubcategoryCollection(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);
        $this->createRandomSubcategory($resource_type_id, $category_id);
        $this->createRandomSubcategory($resource_type_id, $category_id);
        $this->createRandomSubcategory($resource_type_id, $category_id);

        $response = $this->fetchSubcategoryCollection([
            'resource_type_id' => $resource_type_id,
            'category_id' => $category_id,
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 3);
        $response->assertHeader('X-Count', 3);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesSubcategorySchema($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function allocatedExpenseSubcategoryCollectionPagination(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);
        $this->createRandomSubcategory($resource_type_id, $category_id);
        $this->createRandomSubcategory($resource_type_id, $category_id);
        $this->createRandomSubcategory($resource_type_id, $category_id);

        $response = $this->fetchSubcategoryCollection([
            'resource_type_id' => $resource_type_id,
            'category_id' => $category_id,
            'offset' => 0,
            'limit' => 2,
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 3);
        $response->assertHeader('X-Count', 2);
        $response->assertHeader('X-Offset', 0);
        $response->assertHeader('X-Limit', 2);
        $response->assertHeader('X-Link-Previous', "");
        $response->assertHeader('X-Link-Next', '/v3/resource-types/' . $resource_type_id . '/categories/' . $category_id . '/subcategories?offset=2&limit=2');

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesSubcategorySchema($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function allocatedExpenseSubcategoryCollectionPaginationPrevious(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);
        $this->createRandomSubcategory($resource_type_id, $category_id);
        $this->createRandomSubcategory($resource_type_id, $category_id);
        $this->createRandomSubcategory($resource_type_id, $category_id);

        $response = $this->fetchSubcategoryCollection([
            'resource_type_id' => $resource_type_id,
            'category_id' => $category_id,
            'offset' => 2,
            'limit' => 2,
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 3);
        $response->assertHeader('X-Count', 1);
        $response->assertHeader('X-Offset', 2);
        $response->assertHeader('X-Limit', 2);
        $response->assertHeader('X-Link-Previous', '/v3/resource-types/' . $resource_type_id . '/categories/' . $category_id . '/subcategories?offset=0&limit=2');
        $response->assertHeader('X-Link-Next', '');

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesSubcategorySchema($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function allocatedExpenseSubcategoryCollectionSearchDescription(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $search_string = $this->faker->text(100);

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);
        $this->createRandomSubcategory($resource_type_id, $category_id);
        $this->createRandomSubcategory($resource_type_id, $category_id, ['description' => $search_string]);
        $this->createRandomSubcategory($resource_type_id, $category_id);

        $response = $this->fetchSubcategoryCollection([
            'resource_type_id' => $resource_type_id,
            'category_id' => $category_id,
            'search' => 'description:' . $search_string,
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Search', 'description:' . urlencode($search_string));
        $response->assertHeader('X-Count', 1);
        $this->assertEquals($search_string, $response->json()[0]['description']);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesSubcategorySchema($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function allocatedExpenseSubcategoryCollectionSearchName(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $search_string = $this->faker->text(25);

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);
        $this->createRandomSubcategory($resource_type_id, $category_id);
        $this->createRandomSubcategory($resource_type_id, $category_id, ['name' => $search_string]);
        $this->createRandomSubcategory($resource_type_id, $category_id);

        $response = $this->fetchSubcategoryCollection([
            'resource_type_id' => $resource_type_id,
            'category_id' => $category_id,
            'search' => 'name:' . $search_string,
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Search', 'name:' . urlencode($search_string));
        $response->assertHeader('X-Count', 1);
        $this->assertEquals($search_string, $response->json()[0]['name']);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesSubcategorySchema($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function allocatedExpenseSubCategoryCollectionSortCreated(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);

        $this->createRandomSubcategory($resource_type_id, $category_id);
        $this->createRandomSubcategory($resource_type_id, $category_id);
        sleep(2); // Ensure the created_at timestamps are different
        $this->createRandomSubcategory($resource_type_id, $category_id, ['name' => 'created-last']);

        $response = $this->fetchSubcategoryCollection([
            'resource_type_id' => $resource_type_id,
            'category_id' => $category_id,
            'sort'=>'created:desc'
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Sort', 'created:desc');
        $response->assertHeader('X-Count', 3);
        $this->assertEquals('created-last', $response->json()[0]['name']);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesSubcategorySchema($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function allocatedExpenseSubCategoryCollectionSortDescription(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);

        $this->createRandomSubcategory($resource_type_id, $category_id);
        $this->createRandomSubcategory($resource_type_id, $category_id, ['description' => 'AAAAAAAAAAAB']);
        $this->createRandomSubcategory($resource_type_id, $category_id);

        $response = $this->fetchSubcategoryCollection([
            'resource_type_id' => $resource_type_id,
            'category_id' => $category_id,
            'sort'=>'description:desc'
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Sort', 'description:desc');
        $response->assertHeader('X-Count', 3);
        $this->assertEquals('AAAAAAAAAAAB', $response->json()[2]['description']);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesSubcategorySchema($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function allocatedExpenseSubCategoryCollectionSortName(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);

        $this->createRandomSubcategory($resource_type_id, $category_id);
        $this->createRandomSubcategory($resource_type_id, $category_id, ['name' => 'AAAAAAAAAAAB']);
        $this->createRandomSubcategory($resource_type_id, $category_id);

        $response = $this->fetchSubcategoryCollection([
            'resource_type_id' => $resource_type_id,
            'category_id' => $category_id,
            'sort'=>'name:asc'
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Sort', 'name:asc');
        $response->assertHeader('X-Count', 3);
        $this->assertEquals('AAAAAAAAAAAB', $response->json()[0]['name']);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesSubcategorySchema($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function budgetProSubcategoryCollection(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $resource_type_id = $this->createBudgetProResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);
        $this->createRandomSubcategory($resource_type_id, $category_id);
        $this->createRandomSubcategory($resource_type_id, $category_id);
        $this->createRandomSubcategory($resource_type_id, $category_id);

        $response = $this->fetchSubcategoryCollection([
            'resource_type_id' => $resource_type_id,
            'category_id' => $category_id,
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 3);
        $response->assertHeader('X-Count', 3);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesSubcategorySchema($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function budgetSubcategoryCollection(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $resource_type_id = $this->createBudgetResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);
        $this->createRandomSubcategory($resource_type_id, $category_id);
        $this->createRandomSubcategory($resource_type_id, $category_id);
        $this->createRandomSubcategory($resource_type_id, $category_id);

        $response = $this->fetchSubcategoryCollection([
            'resource_type_id' => $resource_type_id,
            'category_id' => $category_id,
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 3);
        $response->assertHeader('X-Count', 3);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesSubcategorySchema($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function gameSubcategoryCollection(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $resource_type_id = $this->createGameResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);
        $this->createRandomSubcategory($resource_type_id, $category_id);
        $this->createRandomSubcategory($resource_type_id, $category_id);
        $this->createRandomSubcategory($resource_type_id, $category_id);

        $response = $this->fetchSubcategoryCollection([
            'resource_type_id' => $resource_type_id,
            'category_id' => $category_id,
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 3);
        $response->assertHeader('X-Count', 3);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesSubcategorySchema($json);
        }
    }
}
