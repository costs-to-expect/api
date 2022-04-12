<?php

namespace Tests\Feature\Http\Controllers;

use App\User;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    /** @test */
    public function check_success(): void
    {
        $this->actingAs(User::find(1));

        $this->get('v2/auth/check')->assertExactJson(['auth'=>true]);
    }

    /** @test */
    public function check_false(): void
    {
        $this->get('v2/auth/check')->assertExactJson(['auth'=>false]);
    }

    /** @test */
    public function create_new_password_errors_with_invalid_email(): void
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
    public function create_new_password_errors_with_invalid_token(): void
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
            route('auth.create-new-password', ['email' => $email, 'token' => $this->faker->uuid]),
            [
                'password' => $password,
                'password_confirmation' => $password
            ]
        );

        $response->assertStatus(404);
    }

    /** @test */
    public function create_new_password_errors_with_invalid_token_and_email(): void
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
    public function create_new_password_errors_with_no_payload(): void
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
    public function create_new_password_success(): void
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
    public function create_password_errors_with_invalid_email(): void
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
    public function create_password_errors_with_invalid_token(): void
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
    public function create_password_errors_with_invalid_token_and_email(): void
    {
        $response = $this->post(
            route('auth.create-password', ['email' => $this->faker->email, 'token' => $this->faker->uuid]),
            []
        );

        $response->assertStatus(401);
    }

    /** @test */
    public function create_password_fails_with_no_payload(): void
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
    public function create_password_fails_with_invalid_payload(): void
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
    public function create_password_success(): void
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
    public function forgot_password_errors_with_bad_email(): void
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
    public function forgot_password_errors_with_no_payload(): void
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
    public function login_errors_with_bad_email(): void
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
    public function login_errors_with_bad_password(): void
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
    public function login_success(): void
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
    public function login_errors_with_no_email(): void
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
    public function login_errors_with_no_name(): void
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
    public function login_errors_with_no_payload(): void
    {
        $response = $this->post(
            route('auth.login'),
            [
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function registration_errors_with_bad_email(): void
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
    public function registration_errors_with_no_email(): void
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
    public function registration_errors_with_no_name(): void
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
    public function registration_errors_with_no_payload(): void
    {
        $response = $this->post(
            route('auth.register'),
            []
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function registration_success(): void
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
    public function update_password_fails_mismatched_passwords(): void
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
    public function update_password_fails_no_payload(): void
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
    public function update_password_success(): void
    {
        $this->actingAs(User::find($this->fetchRandomUser()->id));

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
    public function update_profile_fails_bad_email(): void
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
    public function update_profile_fails_no_payload(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->post(
            'v2/auth/update-profile',
            []
        );

        $response->assertStatus(400);
    }

    /** @test */
    public function update_profile_success(): void
    {
        $this->actingAs(User::find($this->fetchRandomUser()->id));

        $response = $this->post(
            'v2/auth/update-profile',
            [
                'name' => $this->faker->name
            ]
        );

        $response->assertStatus(204);
    }

    /** @test */
    public function user_success(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->get('v2/auth/user');

        $response->assertStatus(200);
    }
}
