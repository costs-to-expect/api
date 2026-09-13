<?php

namespace Tests\Action\Http\Controllers;

use Tests\TestCase;

final class AuthenticationTest extends TestCase
{
    public function testCheckSuccess(): void
    {
        $this->actingAs($this->createUser());

        $this->get('v3/auth/check')->assertExactJson(['auth'=>true]);
    }

    public function testCheckFalse(): void
    {
        $this->get('v3/auth/check')->assertExactJson(['auth'=>false]);
    }

    public function testCreateNewPasswordErrorsWithInvalidEmail(): void
    {
        $email = $this->faker->email;
        $password = $this->faker->password(12);

        $response = $this->post(
            route('auth.register'),
            [
                'name' => $this->faker->name,
                'email' => $email
            ]
        );

        $response->assertStatus(201);

        $token = $response->json('uris.create-password.parameters.token');

        $response = $this->post(
            route('auth.create-password', ['email' => $email, 'token' => $token]),
            [
                'password' => $password,
                'password_confirmation' => $password
            ]
        );

        $response->assertStatus(204);

        $response = $this->post(
            route('auth.forgot-password'),
            [
                'email' => $email
            ]
        );

        $response->assertStatus(201);

        $token = $response->json('uris.create-new-password.parameters.encrypted_token');

        $response = $this->post(
            route('auth.create-new-password', ['email' => $this->faker->email, 'encrypted_token' => $token]),
            [
                'password' => $password,
                'password_confirmation' => $password
            ]
        );

        $response->assertStatus(404);
    }

    public function testCreateNewPasswordErrorsWithInvalidToken(): void
    {
        $email = $this->faker->email;
        $password = $this->faker->password(12);

        $response = $this->post(
            route('auth.register'),
            [
                'name' => $this->faker->name,
                'email' => $email
            ]
        );

        $response->assertStatus(201);

        $token = $response->json('uris.create-password.parameters.token');

        $response = $this->post(
            route('auth.create-password', ['email' => $email, 'token' => $token]),
            [
                'password' => $password,
                'password_confirmation' => $password
            ]
        );

        $response->assertStatus(204);

        $response = $this->post(
            route('auth.forgot-password'),
            [
                'email' => $email
            ]
        );

        $response->assertStatus(201);

        $response = $this->post(
            route('auth.create-new-password', ['email' => $email, 'encrypted_token' => $this->faker->uuid]),
            [
                'password' => $password,
                'password_confirmation' => $password
            ]
        );

        $response->assertStatus(404);
    }

    public function testCreateNewPasswordErrorsWithInvalidTokenAndEmail(): void
    {
        $email = $this->faker->email;
        $password = $this->faker->password(12);

        $response = $this->post(
            route('auth.register'),
            [
                'name' => $this->faker->name,
                'email' => $email
            ]
        );

        $response->assertStatus(201);

        $token = $response->json('uris.create-password.parameters.token');

        $response = $this->post(
            route('auth.create-password', ['email' => $email, 'token' => $token]),
            [
                'password' => $password,
                'password_confirmation' => $password
            ]
        );

        $response->assertStatus(204);

        $response = $this->post(
            route('auth.forgot-password'),
            [
                'email' => $email
            ]
        );

        $response->assertStatus(201);

        $response = $this->post(
            route('auth.create-new-password', ['email' => $this->faker->colorName, 'encrypted_token' => $this->faker->colorName]),
            [
                'password' => $password,
                'password_confirmation' => $password
            ]
        );

        $response->assertStatus(404);
    }

    public function testCreateNewPasswordErrorsWithNoPayload(): void
    {
        $email = $this->faker->email;
        $password = $this->faker->password(12);

        $response = $this->post(
            route('auth.register'),
            [
                'name' => $this->faker->name,
                'email' => $email
            ]
        );

        $response->assertStatus(201);

        $token = $response->json('uris.create-password.parameters.token');

        $response = $this->post(
            route('auth.create-password', ['email' => $email, 'token' => $token]),
            [
                'password' => $password,
                'password_confirmation' => $password
            ]
        );

        $response->assertStatus(204);

        $response = $this->post(
            route('auth.forgot-password'),
            [
                'email' => $email
            ]
        );

        $response->assertStatus(201);

        $token = $response->json('uris.create-new-password.parameters.encrypted_token');

        $response = $this->post(
            route('auth.create-new-password', ['email' => $email, 'encrypted_token' => $token]),
            []
        );

        $response->assertStatus(422);
    }

    public function testCreateNewPasswordSuccess(): void
    {
        $email = $this->faker->email;
        $password = $this->faker->password(12);

        $response = $this->post(
            route('auth.register'),
            [
                'name' => $this->faker->name,
                'email' => $email
            ]
        );

        $response->assertStatus(201);

        $token = $response->json('uris.create-password.parameters.token');

        $response = $this->post(
            route('auth.create-password', ['email' => $email, 'token' => $token]),
            [
                'password' => $password,
                'password_confirmation' => $password
            ]
        );

        $response->assertStatus(204);

        $response = $this->post(
            route('auth.forgot-password'),
            [
                'email' => $email
            ]
        );

        $response->assertStatus(201);

        $token = $response->json('uris.create-new-password.parameters.encrypted_token');

        $response = $this->post(
            route('auth.create-new-password', ['email' => $email, 'encrypted_token' => $token]),
            [
                'password' => $password,
                'password_confirmation' => $password
            ]
        );

        $response->assertStatus(204);
    }

    public function testCreatePasswordErrorsWithInvalidEmail(): void
    {
        $response = $this->post(
            route('auth.register'),
            [
                'name' => $this->faker->name,
                'email' => $this->faker->email
            ]
        );

        $response->assertStatus(201);

        $token = $response->json('uris.create-password.parameters.token');

        $response = $this->post(
            route('auth.create-password', ['email' => $this->faker->email, 'token' => $token]),
            [
                'password' => $this->faker->password(12),
                'password_confirmation' => $this->faker->password(12)
            ]
        );

        $response->assertStatus(401);
    }

    public function tesCreatePasswordErrorsWithInvalidToken(): void
    {
        $email = $this->faker->email;

        $response = $this->post(
            route('auth.register'),
            [
                'name' => $this->faker->name,
                'email' => $email
            ]
        );

        $response->assertStatus(201);

        $response = $this->post(
            route('auth.create-password', ['email' => $email, 'token' => $this->faker->uuid]),
            [
                'password' => $this->faker->password(12),
                'password_confirmation' => $this->faker->password(12)
            ]
        );

        $response->assertStatus(401);
    }

    public function testCreatePasswordErrorsWithInvalidTokenAndEmail(): void
    {
        $response = $this->post(
            route('auth.create-password', ['email' => $this->faker->email, 'token' => $this->faker->uuid]),
            []
        );

        $response->assertStatus(401);
    }

    public function testCreatePasswordFailsWithNoPayload(): void
    {
        $email = $this->faker->email;

        $response = $this->post(
            route('auth.register'),
            [
                'name' => $this->faker->name,
                'email' => $email
            ]
        );

        $response->assertStatus(201);

        $token = $response->json('uris.create-password.parameters.token');

        $response = $this->post(
            route('auth.create-password', ['email' => $email, 'token' => $token]),
            [
            ]
        );

        $response->assertStatus(422);
    }

    public function testCreatePasswordFailsWithInvalidPayload(): void
    {
        $email = $this->faker->email;

        $response = $this->post(
            route('auth.register'),
            [
                'name' => $this->faker->name,
                'email' => $email
            ]
        );

        $response->assertStatus(201);

        $token = $response->json('uris.create-password.parameters.token');

        $response = $this->post(
            route('auth.create-password', ['email' => $email, 'token' => $token]),
            [
                'password' => $this->faker->password(12),
                'password_confirmation' => $this->faker->password(12)
            ]
        );

        $response->assertStatus(422);
    }

    public function testCreatePasswordSuccess(): void
    {
        $email = $this->faker->email;
        $password = $this->faker->password(12);

        $response = $this->post(
            route('auth.register'),
            [
                'name' => $this->faker->name,
                'email' => $email
            ]
        );

        $response->assertStatus(201);

        $token = $response->json('uris.create-password.parameters.token');

        $response = $this->post(
            route('auth.create-password', ['email' => $email, 'token' => $token]),
            [
                'password' => $password,
                'password_confirmation' => $password
            ]
        );

        $response->assertStatus(204);
    }

    public function testForgotPasswordErrorsWithBadEmail(): void
    {
        $response = $this->post(
            route('auth.forgot-password'),
            [
                'email' => 'email.email.com'
            ]
        );

        $response->assertStatus(422);
    }

    public function testForgotPasswordErrorsWithNoPayload(): void
    {
        $response = $this->post(
            route('auth.forgot-password'),
            []
        );

        $response->assertStatus(422);
    }

    public function testForgotPasswordSuccess(): void
    {
        $email = $this->faker->email;
        $password = $this->faker->password(12);

        $response = $this->post(
            route('auth.register'),
            [
                'name' => $this->faker->name,
                'email' => $email
            ]
        );

        $response->assertStatus(201);

        $token = $response->json('uris.create-password.parameters.token');

        $response = $this->post(
            route('auth.create-password', ['email' => $email, 'token' => $token]),
            [
                'password' => $password,
                'password_confirmation' => $password
            ]
        );

        $response->assertStatus(204);

        $response = $this->post(
            route('auth.forgot-password'),
            [
                'email' => $email
            ]
        );

        $response->assertStatus(201);
    }

    public function testLoginErrorsWithBadEmail(): void
    {
        $email = $this->faker->email;
        $password = $this->faker->password(12);

        $response = $this->post(
            route('auth.register'),
            [
                'name' => $this->faker->name,
                'email' => $email
            ]
        );

        $response->assertStatus(201);

        $token = $response->json('uris.create-password.parameters.token');

        $response = $this->post(
            route('auth.create-password', ['email' => $email, 'token' => $token]),
            [
                'password' => $password,
                'password_confirmation' => $password
            ]
        );

        $response->assertStatus(204);

        $response = $this->post(
            route('auth.login'),
            [
                'email' => $this->faker->email,
                'password' => $password,
            ]
        );

        $response->assertStatus(422);
    }

    public function testLoginErrorsWithBadPassword(): void
    {
        $email = $this->faker->email;
        $password = $this->faker->password(12);

        $response = $this->post(
            route('auth.register'),
            [
                'name' => $this->faker->name,
                'email' => $email
            ]
        );

        $response->assertStatus(201);

        $token = $response->json('uris.create-password.parameters.token');

        $response = $this->post(
            route('auth.create-password', ['email' => $email, 'token' => $token]),
            [
                'password' => $password,
                'password_confirmation' => $password
            ]
        );

        $response->assertStatus(204);

        $response = $this->post(
            route('auth.login'),
            [
                'email' => $email,
                'password' => $this->faker->password(12),
            ]
        );

        $response->assertStatus(422);
    }

    public function testLoginSuccess(): void
    {
        $email = $this->faker->email;
        $password = $this->faker->password(12);

        $response = $this->post(
            route('auth.register'),
            [
                'name' => $this->faker->name,
                'email' => $email
            ]
        );

        $response->assertStatus(201);

        $token = $response->json('uris.create-password.parameters.token');

        $response = $this->post(
            route('auth.create-password', ['email' => $email, 'token' => $token]),
            [
                'password' => $password,
                'password_confirmation' => $password
            ]
        );

        $response->assertStatus(204);

        $response = $this->post(
            route('auth.login'),
            [
                'email' => $email,
                'password' => $password
            ]
        );

        $response->assertStatus(201);
    }

    public function testLoginErrorsWithNoEmail(): void
    {
        $response = $this->post(
            route('auth.login'),
            [
                'name' => $this->faker->name,
            ]
        );

        $response->assertStatus(422);
    }

    public function testLoginErrorsWithNoName(): void
    {
        $response = $this->post(
            route('auth.login'),
            [
                'email' => $this->faker->email,
            ]
        );

        $response->assertStatus(422);
    }

    public function testLoginErrorsWithNoPayload(): void
    {
        $response = $this->post(
            route('auth.login'),
            [
            ]
        );

        $response->assertStatus(422);
    }

    public function testRegistrationErrorsWithBadEmail(): void
    {
        $response = $this->post(
            route('auth.register'),
            [
                'name' => $this->faker->name,
                'email' => 'email.email.com'
            ]
        );

        $response->assertStatus(422);
    }

    public function testRegistrationErrorsWithNoEmail(): void
    {
        $response = $this->post(
            route('auth.register'),
            [
                'name' => $this->faker->name
            ]
        );

        $response->assertStatus(422);
    }

    public function testRegistrationErrorsWithNoName(): void
    {
        $response = $this->post(
            route('auth.register'),
            [
                'email' => $this->faker->email
            ]
        );

        $response->assertStatus(422);
    }

    public function testRegistrationErrorsWithNonUniqueEmail(): void
    {
        $email = $this->faker->email;

        $response = $this->post(
            route('auth.register'),
            [
                'email' => $email,
                'name' => $this->faker->name
            ]
        );

        $response->assertStatus(201);

        $response = $this->post(
            route('auth.register'),
            [
                'email' => $email,
                'name' => $this->faker->name
            ]
        );

        $response->assertStatus(422);
    }

    public function testRegistrationErrorsWithNoPayload(): void
    {
        $response = $this->post(
            route('auth.register'),
            []
        );

        $response->assertStatus(422);
    }

    public function testRegistrationSuccess(): void
    {
        $response = $this->post(
            route('auth.register'),
            [
                'name' => $this->faker->name,
                'email' => $this->faker->email
            ]
        );

        $response->assertStatus(201);
    }

    public function testRegistrationSuccessSetRegisteredVia(): void
    {
        $response = $this->post(
            route('auth.register'),
            [
                'name' => $this->faker->name,
                'email' => $this->faker->email,
                'registered_via' => 'budget-pro'
            ]
        );

        $response->assertStatus(201);
    }

    public function testUpdatePasswordFailsMismatchedPasswords(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->post(
            'v3/auth/update-password',
            [
                'password' => $this->faker->password(12),
                'password_confirmation' => $this->faker->password(12)
            ]
        );

        $response->assertStatus(422);
    }

    public function testUpdatePasswordFailsNoPayload(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->post(
            'v3/auth/update-password',
            [
            ]
        );

        $response->assertStatus(422);
    }

    public function testUpdatePasswordSuccess(): void
    {
        $this->createUserAndReturnId();
        
        $this->actingAs($this->createUser());

        $new_password = $this->faker->password(12);

        $response = $this->post(
            'v3/auth/update-password',
            [
                'password' => $new_password,
                'password_confirmation' => $new_password
            ]
        );

        $response->assertStatus(204);
    }

    public function testUpdateProfileFailsBadEmail(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->post(
            'v3/auth/update-profile',
            [
                'email' => 'email.email.com'
            ]
        );

        $response->assertStatus(422);
    }

    public function testUpdateProfileFailsNoPayload(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->post(
            'v3/auth/update-profile',
            []
        );

        $response->assertStatus(400);
    }

    public function testUpdateProfileSuccess(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->post(
            'v3/auth/update-profile',
            [
                'name' => $this->faker->name
            ]
        );

        $response->assertStatus(204);
    }

    public function testUserSuccess(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->get('v3/auth/user');

        $response->assertStatus(200);
    }

    public function testLogoutSuccess(): void
    {
        $user = $this->createUser();
        $plainTextToken = $user->createToken('test-device')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $plainTextToken)->get('v3/auth/logout');

        $response->assertStatus(200);

        $this->assertEquals(
            0,
            \Illuminate\Support\Facades\DB::table('personal_access_tokens')->where('tokenable_id', $user->id)->count()
        );
    }

    public function testLogoutFailsUnauthenticated(): void
    {
        $response = $this->get('v3/auth/logout');

        $response->assertStatus(403);
    }

    public function testMigrateBudgetProRequestDeleteSuccess(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->post('v3/auth/user/migrate/budget-pro/request-migration', []);

        $response->assertStatus(201);
    }

    public function testMigrateBudgetProRequestDeleteFailsUnauthenticated(): void
    {
        $response = $this->post('v3/auth/user/migrate/budget-pro/request-migration', []);

        $response->assertStatus(403);
    }

    public function testRequestDeleteSuccess(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->post('v3/auth/user/request-delete', []);

        $response->assertStatus(201);
    }

    public function testRequestDeleteFailsUnauthenticated(): void
    {
        $response = $this->post('v3/auth/user/request-delete', []);

        $response->assertStatus(403);
    }

    public function testRequestResourceTypeDeleteSuccess(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();

        $response = $this->post(
            route('auth.user.request-resource-type-delete', ['permitted_resource_type_id' => $resource_type_id]),
            []
        );

        $response->assertStatus(201);
    }

    public function testRequestResourceTypeDeleteFailsNotPermitted(): void
    {
        $this->actingAs($this->createUser());

        $resource_type = \App\Models\ResourceType::query()
            ->join('permitted_user', 'resource_type.id', '=', 'permitted_user.resource_type_id')
            ->where('permitted_user.user_id', '=', 1)
            ->first();

        if ($resource_type === null) {
            $this->fail('Unable to fetch a resource type for testing in');
        }

        $other_resource_type_id = (new \App\HttpRequest\Hash())->encode('resource-type', $resource_type->id);

        $response = $this->post(
            route('auth.user.request-resource-type-delete', ['permitted_resource_type_id' => $other_resource_type_id]),
            []
        );

        $response->assertStatus(404);
    }

    public function testRequestResourceTypeDeleteFailsUnauthenticated(): void
    {
        $response = $this->post(
            route('auth.user.request-resource-type-delete', ['permitted_resource_type_id' => 'ABCDEDFGFG']),
            []
        );

        $response->assertStatus(403);
    }

    public function testRequestResourceDeleteSuccess(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $response = $this->post(
            route('auth.user.request-resource-delete', [
                'permitted_resource_type_id' => $resource_type_id,
                'resource_id' => $resource_id
            ]),
            []
        );

        $response->assertStatus(201);
    }

    public function testRequestResourceDeleteFailsUnauthenticated(): void
    {
        $response = $this->post(
            route('auth.user.request-resource-delete', [
                'permitted_resource_type_id' => 'ABCDEDFGFG',
                'resource_id' => 'ABCDEDFGFG'
            ]),
            []
        );

        $response->assertStatus(403);
    }

    public function testDeleteTokenSuccess(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $token = $user->createToken('a-device');

        $response = $this->delete(route('auth.user.token.delete', ['token_id' => $token->accessToken->id]));

        $response->assertStatus(204);
    }

    public function testDeleteTokenFailsNotFound(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->delete(route('auth.user.token.delete', ['token_id' => 999999]));

        $response->assertStatus(404);
    }

    public function testDeleteTokenFailsUnauthenticated(): void
    {
        $response = $this->delete(route('auth.user.token.delete', ['token_id' => 1]));

        $response->assertStatus(403);
    }
}
