<?php

namespace Tests\View\Http\Controllers;

use Tests\TestCase;

final class RequestErrorLogTest extends TestCase
{
    /** @test */
    public function requestErrorLogCollection(): void
    {
        $this->quickCreateRequestErrorLog(['request_uri' => '/v3/resource-types']);
        $this->quickCreateRequestErrorLog(['request_uri' => '/v3/categories']);

        $response = $this->getToRequestErrorLogList();

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 2);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesErrorLogSchema($json);
        }
    }

    /** @test */
    public function requestErrorLogCollectionPagination(): void
    {
        $this->quickCreateRequestErrorLog();
        $this->quickCreateRequestErrorLog();
        $this->quickCreateRequestErrorLog();

        $response = $this->getToRequestErrorLogList(['offset' => 0, 'limit' => 2]);

        $response->assertStatus(200);
        $response->assertHeader('X-Offset', 0);
        $response->assertHeader('X-Limit', 2);
        $response->assertHeader('X-Count', 2);
        $response->assertHeader('X-Total-Count', 3);
    }

    /** @test */
    public function requestErrorLogCollectionIsPubliclyAccessibleWithoutAuthentication(): void
    {
        // Documents current (deliberately not-yet-fixed) behaviour: this endpoint
        // requires no authentication at all, same as the POST that creates entries.
        $this->quickCreateRequestErrorLog();

        $response = $this->getToRequestErrorLogList();

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 1);
    }

    /** @test */
    public function optionsRequestForRequestErrorLogCollection(): void
    {
        $response = $this->fetchOptionsForRequestErrorLogCollection();
        $response->assertStatus(200);
    }
}
