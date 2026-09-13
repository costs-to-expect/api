<?php

namespace Tests\View\Http\Controllers;

use Tests\TestCase;

final class IndexTest extends TestCase
{
    /** @test */
    public function index(): void
    {
        $response = $this->getToIndex();

        $response->assertStatus(200);
        $this->assertArrayHasKey('version', $response->json('api'));
        $this->assertArrayHasKey('prefix', $response->json('api'));
        $this->assertNotEmpty($response->json('routes'));
    }

    /** @test */
    public function optionsRequestForIndex(): void
    {
        $response = $this->fetchOptionsForIndex();
        $response->assertStatus(200);
    }

    /** @test */
    public function changelog(): void
    {
        $response = $this->getToChangelog();

        $response->assertStatus(200);
        $this->assertNotEmpty($response->json('releases'));
        $this->assertArrayHasKey('release', $response->json('releases')[0]);
    }

    /** @test */
    public function optionsRequestForChangelog(): void
    {
        $response = $this->fetchOptionsForChangelog();
        $response->assertStatus(200);
    }

    /** @test */
    public function apiStatus(): void
    {
        $response = $this->getToStatus();

        $response->assertStatus(200);
        $this->assertEquals('testing', $response->json('environment'));
        $this->assertFalse($response->json('cache'));
    }

    /** @test */
    public function optionsRequestForStatus(): void
    {
        $response = $this->fetchOptionsForStatus();
        $response->assertStatus(200);
    }
}
