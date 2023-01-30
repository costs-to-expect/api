<?php

namespace Tests\View\Http\Controllers;

use App\User;
use Tests\TestCase;

final class ResourceTypeViewTest extends TestCase
{
    /** @test */
    public function optionsRequestForResourceType(): void
    {
        $this->actingAs(User::find(1));
        $resource_type_id = $this->createRandomResourceType();

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
    public function resourceTypeCollection(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->fetchAllResourceTypes();

        $response->assertStatus(200);

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
    public function resourceTypeCollectionPagination(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->fetchAllResourceTypes(['offset'=>1, 'limit'=> 1]);

        $response->assertStatus(200);
        $response->assertHeader('X-Offset', 1);
        $response->assertHeader('X-Limit', 1);

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

        $response = $this->fetchAllResourceTypes(['search'=>'description:resource-search']);

        $response->assertStatus(200);
        $response->assertHeader('X-Search', 'description:resource-search');

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

        $response = $this->fetchAllResourceTypes(['search'=>'name:resource-search']);

        $response->assertStatus(200);
        $response->assertHeader('X-Search', 'name:resource-search');

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
        $this->actingAs(User::find(1));

        $response = $this->fetchAllResourceTypes(['sort'=>'created:asc']);

        $response->assertStatus(200);
        $response->assertHeader('X-Sort', 'created:asc');

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
        $this->actingAs(User::find(1));

        $response = $this->fetchAllResourceTypes(['sort'=>'name:asc']);

        $response->assertStatus(200);
        $response->assertHeader('X-Sort', 'name:asc');

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

        $response = $this->fetchAllResourceTypes(['offset'=>0, 'limit'=> 1]);
        $response->assertStatus(200);

        $resource_type_id = $response->json()[0]['id'];

        $response = $this->fetchResourceType(['resource_type_id'=> $resource_type_id]);
        $response->assertStatus(200);

        $this->assertJsonMatchesResourceTypeSchema($response->content());
    }

    /** @test */
    public function resourceTypeShowWithParameterIncludePermittedUsers(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->fetchAllResourceTypes(['offset'=>0, 'limit'=> 1]);
        $response->assertStatus(200);

        $resource_type_id = $response->json()[0]['id'];

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

        $response = $this->fetchAllResourceTypes(['offset'=>0, 'limit'=> 1]);
        $response->assertStatus(200);

        $resource_type_id = $response->json()[0]['id'];

        $this->createRandomResource($resource_type_id);
        $this->createRandomResource($resource_type_id);

        $response = $this->fetchResourceType([
            'resource_type_id'=> $resource_type_id,
            'include-resources' => true
        ]);
        $response->assertStatus(200);

        $this->assertJsonMatchesResourceTypeWhichIncludesResourcesSchema($response->content());
    }
}
