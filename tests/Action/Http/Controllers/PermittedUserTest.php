<?php

namespace Tests\Action\Http\Controllers;

use Tests\TestCase;

final class PermittedUserTest extends TestCase
{
    /** @test */
    public function createPermittedUserFailsNoPayload(): void
    {
        $this->actingAs($this->createUser());

        $id = $this->quickCreateAllocatedExpenseResourceType();

        $response = $this->postToPermittedUserCreate(
            $id,
            []
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createPermittedUserFailsUserDoesNotExist(): void
    {
        $this->actingAs($this->createUser());

        $id = $this->quickCreateAllocatedExpenseResourceType();

        $response = $this->postToPermittedUserCreate(
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
        $this->actingAs($this->createUser());

        $id = $this->quickCreateAllocatedExpenseResourceType();
        $user = $this->createUser();

        $response = $this->postToPermittedUserCreate(
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
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $user = $this->createUser();

        $response = $this->postToPermittedUserCreate(
            $resource_type_id,
            [
                'email' => $user->email,
            ]
        );

        $response->assertStatus(204);

        $response = $this->getToPermittedUserList(['resource_type_id'=> $resource_type_id]);
        $response->assertStatus(200);

        $permitted_user_id = $response->json()[1]['id'];

        $response = $this->deleteToPermittedUserDelete($resource_type_id, $permitted_user_id);
        $response->assertStatus(204);
    }
}
