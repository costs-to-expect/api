<?php

namespace Tests\Feature\Http\Controllers;

use App\User;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    /** @test */
    public function checkSuccess(): void
    {
        $this->actingAs(User::find(1));

        $this->get('v2/auth/check')->assertExactJson(['auth'=>true]);
    }

    /** @test */
    public function checkFalse(): void
    {
        $this->get('v2/auth/check')->assertExactJson(['auth'=>false]);
    }

    /** @test */
    public function createNewPasswordErrorsWithInvalidEmail(): void
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

        $token = $response->json('uris.create-new-password.parameters.token');

        $response = $this->post(
            route('auth.create-new-password', ['email' => $this->faker->email, 'token' => $token]),
            [
                'password' => $password,
                'password_confirmation' => $password
            ]
        );

        $response->assertStatus(404);
    }

    /** @test */
    public function createNewPasswordErrorsWithInvalidToken(): void
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
            route('auth.create-new-password', ['email' => $email, 'token' => $this->faker->uuid]),
            [
                'password' => $password,
                'password_confirmation' => $password
            ]
        );

        $response->assertStatus(404);
    }

    /** @test */
    public function createNewPasswordErrorsWithInvalidTokenAndEmail(): void
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

        $token = $response->json('uris.create-new-password.parameters.token');

        $response = $this->post(
            route('auth.create-new-password', ['email' => $this->faker->colorName, 'token' => $this->faker->colorName]),
            [
                'password' => $password,
                'password_confirmation' => $password
            ]
        );

        $response->assertStatus(404);
    }

    /** @test */
    public function createNewPasswordErrorsWithNoPayload(): void
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

        $token = $response->json('uris.create-new-password.parameters.token');

        $response = $this->post(
            route('auth.create-new-password', ['email' => $email, 'token' => $token]),
            []
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function createNewPasswordSuccess(): void
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

        $token = $response->json('uris.create-new-password.parameters.token');

        $response = $this->post(
            route('auth.create-new-password', ['email' => $email, 'token' => $token]),
            [
                'password' => $password,
                'password_confirmation' => $password
            ]
        );

        $response->assertStatus(204);
    }

    /** @test */
    public function createPasswordErrorsWithInvalidEmail(): void
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

    /** @test */
    public function createPasswordErrorsWithInvalidToken(): void
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

    /** @test */
    public function createPasswordErrorsWithInvalidTokenAndEmail(): void
    {
        $response = $this->post(
            route('auth.create-password', ['email' => $this->faker->email, 'token' => $this->faker->uuid]),
            []
        );

        $response->assertStatus(401);
    }

    /** @test */
    public function createPasswordFailsWithNoPayload(): void
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

    /** @test */
    public function createPasswordFailsWithInvalidPayload(): void
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

    /** @test */
    public function createPasswordSuccess(): void
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

    /** @test */
    public function forgotPasswordErrorsWithBadEmail(): void
    {
        $response = $this->post(
            route('auth.forgot-password'),
            [
                'email' => 'email.email.com'
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function forgotPasswordErrorsWithNoPayload(): void
    {
        $response = $this->post(
            route('auth.forgot-password'),
            []
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function forgot_password_success(): void
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

    /** @test */
    public function loginErrorsWithBadEmail(): void
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

        $response->assertStatus(401);
    }

    /** @test */
    public function loginErrorsWithBadPassword(): void
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

        $response->assertStatus(401);
    }

    /** @test */
    public function loginSuccess(): void
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

    /** @test */
    public function loginErrorsWithNoEmail(): void
    {
        $response = $this->post(
            route('auth.login'),
            [
                'name' => $this->faker->name,
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function loginErrorsWithNoName(): void
    {
        $response = $this->post(
            route('auth.login'),
            [
                'email' => $this->faker->email,
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function loginErrorsWithNoPayload(): void
    {
        $response = $this->post(
            route('auth.login'),
            [
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function registrationErrorsWithBadEmail(): void
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

    /** @test */
    public function registrationErrorsWithNoEmail(): void
    {
        $response = $this->post(
            route('auth.register'),
            [
                'name' => $this->faker->name
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function registrationErrorsWithNoName(): void
    {
        $response = $this->post(
            route('auth.register'),
            [
                'email' => $this->faker->email
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function registrationErrorsWithNoPayload(): void
    {
        $response = $this->post(
            route('auth.register'),
            []
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function registrationSuccess(): void
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

    /** @test */
    public function updatePasswordFailsMismatchedPasswords(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->post(
            'v2/auth/update-password',
            [
                'password' => $this->faker->password(12),
                'password_confirmation' => $this->faker->password(12)
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function updatePasswordFailsNoPayload(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->post(
            'v2/auth/update-password',
            [
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function updatePasswordSuccess(): void
    {
        $this->actingAs(User::find($this->getARandomUser()->id));

        $new_password = $this->faker->password(12);

        $response = $this->post(
            'v2/auth/update-password',
            [
                'password' => $new_password,
                'password_confirmation' => $new_password
            ]
        );

        $response->assertStatus(204);
    }

    /** @test */
    public function updateProfileFailsBadEmail(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->post(
            'v2/auth/update-profile',
            [
                'email' => 'email.email.com'
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function updateProfileFailsNoPayload(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->post(
            'v2/auth/update-profile',
            []
        );

        $response->assertStatus(400);
    }

    /** @test */
    public function updateProfileSuccess(): void
    {
        $this->actingAs(User::find($this->getARandomUser()->id));

        $response = $this->post(
            'v2/auth/update-profile',
            [
                'name' => $this->faker->name
            ]
        );

        $response->assertStatus(204);
    }

    /** @test */
    public function userSuccess(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->get('v2/auth/user');

        $response->assertStatus(200);
    }
}
