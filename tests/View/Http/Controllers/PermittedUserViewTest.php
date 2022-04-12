<?php

namespace Tests\View\Http\Controllers;

use App\User;
use Tests\TestCase;

class PermittedUserViewTest extends TestCase
{
    /** @test */
    public function collection(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->getResourceTypes();
        $response->assertStatus(200);

        foreach ($response->json() as $resource_type) {

            $permitted_user_response = $this->getPermittedUsers(['resource_type_id'=> $resource_type['id']]);
            $permitted_user_response->assertStatus(200);

            foreach ($permitted_user_response->json() as $permitted_user) {
                try {
                    $json = json_encode($permitted_user, JSON_THROW_ON_ERROR);
                } catch (\JsonException $e) {
                    $this->fail('Unable to encode the JSON string');
                }

                $this->assertJsonIsPermittedUser($json);
            }
        }
    }

    /** @test */
    public function item(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->getResourceTypes(['offset'=>0, 'limit'=> 1]);
        $response->assertStatus(200);

        $resource_type_id = $response->json()[0]['id'];

        $response = $this->getPermittedUsers(['resource_type_id'=> $resource_type_id]);
        $response->assertStatus(200);

        $permitted_user_id = $response->json()[0]['id'];

        $response = $this->getPermittedUser(['resource_type_id'=> $resource_type_id, 'permitted_user_id' => $permitted_user_id]);
        $response->assertStatus(200);

        $this->assertJsonIsPermittedUser($response->content());
    }
}
