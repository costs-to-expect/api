<?php

namespace Http\Controllers;

use App\User;
use Tests\TestCase;

final class ResourceTest extends TestCase
{
    /** @test */
    public function optionsRequestForAllocatedExpenseResource(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $resource_id = $this->createAllocatedExpenseResource($resource_type_id);

        $response = $this->fetchOptionsForResource([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
        ]);
        $response->assertStatus(200);

        // Resource is the same for all types, we are testing the OPTIONS request for the different item types, until the resources differ later on.
        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/resource.json');
    }

    /** @test */
    public function optionsRequestForAllocatedExpenseResourceCollection(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();

        $response = $this->fetchOptionsForResourceCollection([
            'resource_type_id' => $resource_type_id
        ]);
        $response->assertStatus(200);

        // Resource is the same for all types, we are testing the OPTIONS request for the different item types, until the resources differ later on.
        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/resource-collection.json');
    }

    /** @test */
    public function optionsRequestForBudgetResource(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetResourceType();
        $resource_id = $this->createBudgetResource($resource_type_id);

        $response = $this->fetchOptionsForResource([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
        ]);
        $response->assertStatus(200);

        // Resource is the same for all types, we are testing the OPTIONS request for the different item types, until the resources differ later on.
        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/resource.json');
    }

    /** @test */
    public function optionsRequestForBudgetResourceCollection(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetResourceType();

        $response = $this->fetchOptionsForResourceCollection([
            'resource_type_id' => $resource_type_id
        ]);
        $response->assertStatus(200);

        // Resource is the same for all types, we are testing the OPTIONS request for the different item types, until the resources differ later on.
        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/resource-collection.json');
    }

    /** @test */
    public function optionsRequestForBudgetProResource(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetProResourceType();
        $resource_id = $this->createBudgetProResource($resource_type_id);

        $response = $this->fetchOptionsForResource([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
        ]);
        $response->assertStatus(200);

        // Resource is the same for all types, we are testing the OPTIONS request for the different item types, until the resources differ later on.
        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/resource.json');
    }

    /** @test */
    public function optionsRequestForBudgetProResourceCollection(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createBudgetProResourceType();

        $response = $this->fetchOptionsForResourceCollection([
            'resource_type_id' => $resource_type_id
        ]);
        $response->assertStatus(200);

        // Resource is the same for all types, we are testing the OPTIONS request for the different item types, until the resources differ later on.
        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/resource-collection.json');
    }

    /** @test */
    public function optionsRequestForYahtzeeResource(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createGameResourceType();
        $resource_id = $this->createYahtzeeResource($resource_type_id);

        $response = $this->fetchOptionsForResource([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
        ]);
        $response->assertStatus(200);

        // Resource is the same for all types, we are testing the OPTIONS request for the different item types, until the resources differ later on.
        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/resource.json');
    }

    /** @test */
    public function optionsRequestForYahtzeeResourceCollection(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createGameResourceType();

        $response = $this->fetchOptionsForResourceCollection([
            'resource_type_id' => $resource_type_id
        ]);
        $response->assertStatus(200);

        // Resource is the same for all types, we are testing the OPTIONS request for the different item types, until the resources differ later on.
        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/resource-collection.json');
    }

    /** @test */
    public function optionsRequestForYatzyResource(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createGameResourceType();
        $resource_id = $this->createYatzyResource($resource_type_id);

        $response = $this->fetchOptionsForResource([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
        ]);
        $response->assertStatus(200);

        // Resource is the same for all types, we are testing the OPTIONS request for the different item types, until the resources differ later on.
        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/resource.json');
    }

    /** @test */
    public function optionsRequestForYatzyResourceCollection(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createGameResourceType();

        $response = $this->fetchOptionsForResourceCollection([
            'resource_type_id' => $resource_type_id
        ]);
        $response->assertStatus(200);

        // Resource is the same for all types, we are testing the OPTIONS request for the different item types, until the resources differ later on.
        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/resource-collection.json');
    }
}
