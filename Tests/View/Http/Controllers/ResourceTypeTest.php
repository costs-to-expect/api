<?php

namespace Tests\View\Http\Controllers;

use App\User;
use Tests\TestCase;

final class ResourceTypeTest extends TestCase
{
    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function allocatedExpenseResourceTypeCollection(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $this->createAllocatedExpenseResourceType();
        $this->createAllocatedExpenseResourceType();

        $response = $this->fetchResourceTypeCollection(['item-type'=>$this->item_types['allocated-expense'], 'exclude-public'=>'true']);

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 2);
        $response->assertHeader('X-Count', 2);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesResourceTypeSchema($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function budgetResourceTypeCollection(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $this->createBudgetResourceType();
        $this->createBudgetResourceType();

        $response = $this->fetchResourceTypeCollection(['item-type'=>$this->item_types['budget'], 'exclude-public'=>'true']);

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 2);
        $response->assertHeader('X-Count', 2);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesResourceTypeSchema($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function budgetProResourceTypeCollection(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $this->createBudgetProResourceType();
        $this->createBudgetProResourceType();

        $response = $this->fetchResourceTypeCollection(['item-type'=>$this->item_types['budget-pro'], 'exclude-public'=>'true']);

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 2);
        $response->assertHeader('X-Count', 2);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesResourceTypeSchema($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function gameResourceTypeCollection(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $this->createGameResourceType();
        $this->createGameResourceType();

        $response = $this->fetchResourceTypeCollection(['item-type'=>$this->item_types['game'], 'exclude-public'=>'true']);

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 2);
        $response->assertHeader('X-Count', 2);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesResourceTypeSchema($json);
        }
    }

    /** @test */
    public function optionsRequestForAllocatedExpenseResourceType(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();

        $response = $this->fetchOptionsForResourceType(['resource_type_id' => $resource_type_id]);
        $response->assertStatus(200);

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/resource-type.json');
    }

    /** @test */
    public function optionsRequestForResourceTypeCollection(): void
    {
        $response = $this->fetchOptionsForResourceTypeCollection();
        $response->assertStatus(200);

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/resource-type-collection.json');
    }

    /** @test */
    public function optionsRequestForBudgetResourceType(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetResourceType();

        $response = $this->fetchOptionsForResourceType(['resource_type_id' => $resource_type_id]);
        $response->assertStatus(200);

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/resource-type.json');
    }

    /** @test */
    public function optionsRequestForBudgetProResourceType(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetProResourceType();

        $response = $this->fetchOptionsForResourceType(['resource_type_id' => $resource_type_id]);
        $response->assertStatus(200);

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/resource-type.json');
    }

    /** @test */
    public function optionsRequestForGameResourceType(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createGameResourceType();

        $response = $this->fetchOptionsForResourceType(['resource_type_id' => $resource_type_id]);
        $response->assertStatus(200);

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/resource-type.json');
    }

    /** @test */
    public function resourceTypeCollectionPagination(): void
    {
        $this->actingAs(User::find(1));

        $this->createAllocatedExpenseResourceType();
        $this->createAllocatedExpenseResourceType();
        $this->createAllocatedExpenseResourceType();

        $response = $this->fetchResourceTypeCollection(['offset' => 0, 'limit' => 2]);

        $response->assertStatus(200);
        $response->assertHeader('X-Offset', 0);
        $response->assertHeader('X-Limit', 2);
        $response->assertHeader('X-Link-Previous', "");
        $response->assertHeader('X-Link-Next', "/v3/resource-types?offset=2&limit=2");

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesResourceTypeSchema($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function resourceTypeCollectionSearchDescription(): void
    {
        $this->actingAs(User::find(1));

        $search_string = $this->faker->text(35);

        $this->createAllocatedExpenseResourceType();
        $this->createAllocatedExpenseResourceType(['description' => $search_string]);
        $this->createAllocatedExpenseResourceType();

        $response = $this->fetchResourceTypeCollection(['search'=>'description:' . $search_string]);

        $response->assertStatus(200);
        $response->assertHeader('X-Search', 'description:' . urlencode($search_string));
        $response->assertHeader('X-Count', 1);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesResourceTypeSchema($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function resourceTypeCollectionSearchName(): void
    {
        $this->actingAs(User::find(1));

        $search_string = $this->faker->text(35);

        $this->createAllocatedExpenseResourceType();
        $this->createAllocatedExpenseResourceType(['name' => $search_string]);
        $this->createAllocatedExpenseResourceType();

        $response = $this->fetchResourceTypeCollection(['search'=>'name:' . $search_string]);

        $response->assertStatus(200);
        $response->assertHeader('X-Search', 'name:' . urlencode($search_string));
        $response->assertHeader('X-Count', 1);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesResourceTypeSchema($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function resourceTypeCollectionSortCreated(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $this->createAllocatedExpenseResourceType();
        $this->createAllocatedExpenseResourceType();
        sleep(1); // Ensure the created_at timestamps are different
        $this->createAllocatedExpenseResourceType(['name' => 'created-last']);

        $response = $this->fetchResourceTypeCollection([
            'sort'=>'created:desc'
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Sort', 'created:desc');
        $this->assertEquals('created-last', $response->json()[0]['name']);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesResourceTypeSchema($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function resourceTypeCollectionSortName(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $this->createAllocatedExpenseResourceType();
        $this->createAllocatedExpenseResourceType(['name' => 'AAAAAAAAAAAA']);
        $this->createAllocatedExpenseResourceType();

        $response = $this->fetchResourceTypeCollection([
            'sort'=>'name:asc'
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Sort', 'name:asc');
        $this->assertEquals('AAAAAAAAAAAA', $response->json()[0]['name']);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesResourceTypeSchema($json);
        }
    }

    /** @test */
    public function resourceTypeShow(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();

        $response = $this->fetchResourceType(['resource_type_id'=> $resource_type_id]);
        $response->assertStatus(200);

        $this->assertJsonMatchesResourceTypeSchema($response->content());
    }

    /** @test */
    public function resourceTypeShowWithParameterIncludePermittedUsers(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();

        $response = $this->fetchResourceType([
            'resource_type_id'=> $resource_type_id,
            'include-permitted-users' => true
        ]);
        $response->assertStatus(200);

        $this->assertJsonMatchesResourceTypeWhichIncludesPermittedUsersSchema($response->content());
    }

    /** @test */
    public function resourceTypeShowWithParameterIncludeResource(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();

        $this->createAllocatedExpenseResource($resource_type_id);
        $this->createAllocatedExpenseResource($resource_type_id);

        $response = $this->fetchResourceType([
            'resource_type_id'=> $resource_type_id,
            'include-resources' => true
        ]);
        $response->assertStatus(200);

        $this->assertJsonMatchesResourceTypeWhichIncludesResourcesSchema($response->content());
    }
}
