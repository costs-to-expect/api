<?php

namespace Tests\View\Http\Controllers;

use Tests\TestCase;

final class PermittedUserTest extends TestCase
{
    /** @test */
    public function permittedUserCollection(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->getToResourceTypeList();
        $response->assertStatus(200);

        foreach ($response->json() as $resource_type) {

            $permitted_user_response = $this->getToPermittedUserList(['resource_type_id'=> $resource_type['id']]);
            $permitted_user_response->assertStatus(200);

            foreach ($permitted_user_response->json() as $permitted_user) {
                try {
                    $json = json_encode($permitted_user, JSON_THROW_ON_ERROR);
                } catch (\JsonException $e) {
                    $this->fail('Unable to encode the JSON string');
                }

                $this->assertJsonMatchesPermittedUserSchema($json);
            }
        }
    }

    /** @test */
    public function permittedUserShow(): void
    {
        $this->actingAs($this->createUser());
        
        $this->quickCreateAllocatedExpenseResourceType();

        $response = $this->getToResourceTypeList(['offset'=>0, 'limit'=> 1]);
        $response->assertStatus(200);

        $resource_type_id = $response->json()[0]['id'];

        $response = $this->getToPermittedUserList(['resource_type_id'=> $resource_type_id]);
        $response->assertStatus(200);

        $permitted_user_id = $response->json()[0]['id'];

        $response = $this->getToPermittedUserShow(['resource_type_id'=> $resource_type_id, 'permitted_user_id' => $permitted_user_id]);
        $response->assertStatus(200);

        $this->assertJsonMatchesPermittedUserSchema($response->content());
    }
}
