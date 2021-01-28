<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    /** @test */
    public function create_password_errors_with_invalid_token()
    {
        $response = $this->post(
            route('auth.create-password', ['email' => $this->faker->email, 'token' => $this->faker->uuid]),
            [

            ]
        );

        $response->assertStatus(401);
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
    public function registration_success(): void
    {
        $response = $this->post(
            route('auth.register'),
            [
                'name' => $this->test_account_name,
                'email' => $this->test_account_email
            ]
        );

        echo $this->test_account_name;
        echo $this->test_account_email;

        $this->test_account_create_password_token = $response->json('uris.create-password.parameters.token');

        echo $this->test_account_create_password_token;

        $response->assertStatus(201);
    }

    /**
     * @test
     * @depends registration_success
     */
    public function create_password_errors_with_invalid_payload()
    {

        echo $this->test_account_name;
        echo $this->test_account_email;

        $response = $this->post(
            route('auth.create-password', ['email' => $this->test_account_email, 'token' => $this->test_account_create_password_token]),
            [
                'password' => $this->faker->password,
                'password_confirmation' => $this->faker->password
            ]
        );

        $response->assertStatus(422);
    }
}
