<?php

namespace Tests\Feature\Http\Controllers;

use App\User;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    /** @test */
    public function create_password_errors_with_invalid_email()
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
                'password' => $this->faker->password(10),
                'password_confirmation' => $this->faker->password(10)
            ]
        );

        $response->assertStatus(401);
    }

    /** @test */
    public function create_password_errors_with_invalid_token()
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
                'password' => $this->faker->password(10),
                'password_confirmation' => $this->faker->password(10)
            ]
        );

        $response->assertStatus(401);
    }

    /** @test */
    public function create_password_errors_with_invalid_token_and_email()
    {
        $response = $this->post(
            route('auth.create-password', ['email' => $this->faker->email, 'token' => $this->faker->uuid]),
            []
        );

        $response->assertStatus(401);
    }

    /** @test */
    public function create_password_fails_with_no_payload()
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
    public function create_password_fails_with_invalid_payload()
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
                'password' => $this->faker->password(10),
                'password_confirmation' => $this->faker->password(10)
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function create_password_success()
    {
        $email = $this->faker->email;
        $password = $this->faker->password(10);

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
    public function registration_errors_with_bad_email()
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
    public function registration_errors_with_no_email()
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
    public function registration_errors_with_no_name()
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
    public function registration_errors_with_no_payload()
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
    public function user_success(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->get('v2/auth/user');

        $response->assertStatus(200);
        $this->tearDown();
    }
}
