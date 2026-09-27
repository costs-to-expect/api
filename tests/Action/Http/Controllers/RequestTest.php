<?php

namespace Tests\Action\Http\Controllers;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class RequestTest extends TestCase
{
    #[Test]
    public function createErrorLogFailsNoPayload(): void
    {
        $response = $this->postToRequestErrorLogCreate([]);

        $response->assertStatus(422);
    }

    #[Test]
    public function createErrorLogFailsInvalidSource(): void
    {
        $response = $this->postToRequestErrorLogCreate([
            'method' => 'GET',
            'expected_status_code' => 200,
            'returned_status_code' => 500,
            'request_uri' => '/v3/resource-types',
            'source' => 'not-a-real-source'
        ]);

        $response->assertStatus(422);
    }

    #[Test]
    public function createErrorLogFailsStatusCodeOutOfRange(): void
    {
        $response = $this->postToRequestErrorLogCreate([
            'method' => 'GET',
            'expected_status_code' => 999,
            'returned_status_code' => 500,
            'request_uri' => '/v3/resource-types',
            'source' => 'app'
        ]);

        $response->assertStatus(422);
    }

    #[Test]
    public function createErrorLogSuccess(): void
    {
        $response = $this->postToRequestErrorLogCreate([
            'method' => 'GET',
            'expected_status_code' => 200,
            'returned_status_code' => 500,
            'request_uri' => '/v3/resource-types',
            'source' => 'app'
        ]);

        $response->assertStatus(204);
    }

    #[Test]
    public function createErrorLogSuccessWithDebugField(): void
    {
        $response = $this->postToRequestErrorLogCreate([
            'method' => 'GET',
            'expected_status_code' => 200,
            'returned_status_code' => 500,
            'request_uri' => '/v3/resource-types',
            'source' => 'app',
            'debug' => 'stack trace goes here'
        ]);

        $response->assertStatus(204);

        $response = $this->getToRequestErrorLogList();
        $this->assertEquals('stack trace goes here', $response->json()[0]['debug']);
    }
}
