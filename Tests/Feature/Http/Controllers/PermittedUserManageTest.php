<?php

namespace Tests\Feature\Http\Controllers;

use App\User;
use Tests\TestCase;

class PermittedUserManageTest extends TestCase
{
    /** @test */
    public function create_permitted_user_fails_no_payload(): void
    {
        $this->actingAs(User::find(1));

        $id = $this->createAndReturnResourceTypeId();

        $response = $this->postPermittedUser(
            $id,
            []
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function create_permitted_user_fails_user_does_not_exist(): void
    {
        $this->actingAs(User::find(1));

        $id = $this->createAndReturnResourceTypeId();

        $response = $this->postPermittedUser(
            $id,
            [
                'email' => $this->faker->email
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function create_permitted_user_success(): void
    {
        $this->actingAs(User::find(1));

        $id = $this->createAndReturnResourceTypeId();
        $user = $this->getARandomUser();

        $response = $this->postPermittedUser(
            $id,
            [
                'email' => $user->email,
            ]
        );

        $response->assertStatus(204);
    }

    /** @test */
    public function delete_permitted_user_success(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAndReturnResourceTypeId();
        $user = $this->getARandomUser();

        $response = $this->postPermittedUser(
            $resource_type_id,
            [
                'email' => $user->email,
            ]
        );

        $response->assertStatus(204);

        $response = $this->getPermittedUsers(['resource_type_id'=> $resource_type_id]);
        $response->assertStatus(200);

        $permitted_user_id = $response->json()[1]['id'];

        $response = $this->deletePermittedUser($resource_type_id, $permitted_user_id);
        $response->assertStatus(204);
    }
}
