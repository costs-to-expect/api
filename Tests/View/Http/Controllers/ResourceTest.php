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

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/resource.json');
    }
}
