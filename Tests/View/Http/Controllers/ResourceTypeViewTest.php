<?php

namespace Tests\View\Http\Controllers;

use App\User;
use Tests\TestCase;

final class ResourceTypeViewTest extends TestCase
{
    /** @test */
    public function optionsRequestForResourceTypeCollection(): void
    {
        $response = $this->optionsResourceTypeCollection();
        $response->assertStatus(200);

        $this->assertJsonMatchesSchema($response->content(), 'api/schema/options/resource-type-collection.json');
    }

    /** @test */
    public function resourceTypeCollection(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->getResourceTypes();

        $response->assertStatus(200);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonIsResourceType($json);
        }
    }

    /** @test */
    public function resourceTypeCollectionPagination(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->getResourceTypes(['offset'=>1, 'limit'=> 1]);

        $response->assertStatus(200);
        $response->assertHeader('X-Offset', 1);
        $response->assertHeader('X-Limit', 1);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonIsResourceType($json);
        }
    }

    /** @test */
    public function resourceTypeShow(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->getResourceTypes(['offset'=>0, 'limit'=> 1]);
        $response->assertStatus(200);

        $resource_type_id = $response->json()[0]['id'];

        $response = $this->getResourceType(['resource_type_id'=> $resource_type_id]);
        $response->assertStatus(200);

        $this->assertJsonIsResourceType($response->content());
    }
}
