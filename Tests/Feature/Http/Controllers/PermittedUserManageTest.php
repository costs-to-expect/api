<?php

namespace Tests\Feature\Http\Controllers;

use App\User;
use Tests\TestCase;

final class PermittedUserManageTest extends TestCase
{
    /** @test */
    public function createPermittedUserFailsNoPayload(): void
    {
        $this->actingAs(User::find(1));

        $id = $this->createAllocatedExpenseResourceType();

        $response = $this->createRequestedPermittedUser(
            $id,
            []
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createPermittedUserFailsUserDoesNotExist(): void
    {
        $this->actingAs(User::find(1));

        $id = $this->createAllocatedExpenseResourceType();

        $response = $this->createRequestedPermittedUser(
            $id,
            [
                'email' => $this->faker->email
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createPermittedUserSuccess(): void
    {
        $this->actingAs(User::find(1));

        $id = $this->createAllocatedExpenseResourceType();
        $user = $this->fetchRandomUser();

        $response = $this->createRequestedPermittedUser(
            $id,
            [
                'email' => $user->email,
            ]
        );

        $response->assertStatus(204);
    }

    /** @test */
    public function deletePermittedUserSuccess(): void
    {
        $this->actingAs(User::find(1));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $user = $this->fetchRandomUser();

        $response = $this->createRequestedPermittedUser(
            $resource_type_id,
            [
                'email' => $user->email,
            ]
        );

        $response->assertStatus(204);

        $response = $this->fetchAllPermittedUsers(['resource_type_id'=> $resource_type_id]);
        $response->assertStatus(200);

        $permitted_user_id = $response->json()[1]['id'];

        $response = $this->deleteRequestedPermittedUser($resource_type_id, $permitted_user_id);
        $response->assertStatus(204);
    }
}
