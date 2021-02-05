<?php

namespace Tests\Feature\Http\Controllers;

use App\User;
use Tests\TestCase;

class ResourceTypeManageTest extends TestCase
{
    /** @test */
    public function create_resource_type_fails_no_payload(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->post(
            route('resource-type.create'),
            []
        );

        // Rename the validateAndReturnErrors method

        $response->assertStatus(422);
    }
}
