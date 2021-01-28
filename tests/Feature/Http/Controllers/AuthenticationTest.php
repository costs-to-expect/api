<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;

class AuthenticationTest extends TestCase
{
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

        print_r($response->json());
        die;

        $response->assertStatus(201);
    }
}
