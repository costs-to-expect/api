<?php

namespace Tests\View\Http\Controllers;

use App\User;
use Tests\TestCase;

class ResourceTypeViewTest extends TestCase
{
    /** @test */
    public function collection(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->fetchResourceTypes();

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
    public function collection_pagination(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->fetchResourceTypes(['offset'=>1, 'limit'=> 1]);

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
    public function item(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->fetchResourceTypes(['offset'=>0, 'limit'=> 1]);
        $response->assertStatus(200);

        $resource_type_id = $response->json()[0]['id'];

        $response = $this->fetchResourceType(['resource_type_id'=> $resource_type_id]);
        $response->assertStatus(200);

        $this->assertJsonIsResourceType($response->content());
    }
}
