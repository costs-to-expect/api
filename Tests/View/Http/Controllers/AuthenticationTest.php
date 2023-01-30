<?php

namespace Tests\View\Http\Controllers;

use Tests\TestCase;

final class AuthenticationTest extends TestCase
{
    /** @test */
    public function optionsRequestForCreatePassword(): void
    {
        $response = $this->fetchOptionsForCreatePassword();
        $response->assertStatus(200);

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/auth/options/create-password.json');
    }

    /** @test */
    public function optionsRequestForRegister(): void
    {
        $response = $this->fetchOptionsForRegister();
        $response->assertStatus(200);

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/auth/options/register.json');
    }
}
