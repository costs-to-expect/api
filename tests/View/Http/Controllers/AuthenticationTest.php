<?php

namespace Tests\View\Http\Controllers;

use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    /** @test */
    public function optionsRequestForRegister(): void
    {
        $response = $this->optionsRegister();
        $response->assertStatus(200);

        $this->assertJsonMatchesSchema($response->content(), 'api/schema/auth/options/register.json');
    }
}
