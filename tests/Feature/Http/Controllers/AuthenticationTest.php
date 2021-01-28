<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function registration_shows ()
    {
        $this->refreshDatabase();

        $response = $this->post(route('auth.register'), []);

        $response->assertStatus(422);
    }
}
