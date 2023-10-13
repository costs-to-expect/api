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
    public function optionsRequestForMigrateBudgetProRequestDelete(): void
    {
        $response = $this->fetchOptionsForMigrateBudgetProRequestDelete();
        $response->assertStatus(200);

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/auth/options/migrate-budget-pro-request-delete.json');
    }

    /** @test */
    public function optionsRequestForRegister(): void
    {
        $response = $this->fetchOptionsForRegister();
        $response->assertStatus(200);

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/auth/options/register.json');
    }

    /** @test */
    public function optionsRequestForUpdatePassword(): void
    {
        $response = $this->fetchOptionsForUpdatePassword();
        $response->assertStatus(200);

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/auth/options/update-password.json');
    }

    /** @test */
    public function optionsRequestForUpdateProfile(): void
    {
        $response = $this->fetchOptionsForUpdateProfile();
        $response->assertStatus(200);

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/auth/options/update-profile.json');
    }
}
