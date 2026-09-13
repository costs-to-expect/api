<?php

namespace Tests\View\Http\Controllers;

use Tests\TestCase;

final class CurrencyTest extends TestCase
{
    /** @test */
    public function currencyCollection(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->getToCurrencyList();
        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 8);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesCurrencySchema($json);
        }
    }

    /** @test */
    public function currencyCollectionPagination(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->getToCurrencyList(['offset' => 0, 'limit' => 2]);

        $response->assertStatus(200);
        $response->assertHeader('X-Offset', 0);
        $response->assertHeader('X-Limit', 2);
        $response->assertHeader('X-Count', 2);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesCurrencySchema($json);
        }
    }

    /** @test */
    public function currencyCollectionSearchCode(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->getToCurrencyList(['search' => 'code:USD']);

        $response->assertStatus(200);
        $response->assertHeader('X-Search', 'code:USD');
        $response->assertHeader('X-Count', 1);

        $this->assertEquals('USD', $response->json()[0]['code']);
    }

    /** @test */
    public function currencyCollectionSearchName(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->getToCurrencyList(['search' => 'name:Dollar']);

        $response->assertStatus(200);
        $response->assertHeader('X-Search', 'name:Dollar');
        $response->assertHeader('X-Count', 4);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesCurrencySchema($json);
        }
    }

    /** @test */
    public function currencyCollectionSearchNoResults(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->getToCurrencyList(['search' => 'code:XXX']);

        $response->assertStatus(200);
        $response->assertHeader('X-Count', 0);
    }

    /** @test */
    public function currencyCollectionSortName(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->getToCurrencyList(['sort' => 'name:asc', 'limit' => 1]);

        $response->assertStatus(200);
        $response->assertHeader('X-Sort', 'name:asc');
        $this->assertEquals('Australian Dollar', $response->json()[0]['name']);
    }

    /** @test */
    public function currencyShow(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->getToCurrencyList(['offset' => 0, 'limit' => 1]);
        $response->assertStatus(200);

        $currency_id = $response->json()[0]['id'];

        $response = $this->getToCurrencyShow(['currency_id' => $currency_id]);
        $response->assertStatus(200);

        $this->assertJsonMatchesCurrencySchema($response->content());
    }

    /** @test */
    public function currencyShowInvalidId(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->getToCurrencyShow(['currency_id' => 'xxxxxxxxxx']);
        $response->assertStatus(403);
    }

    /** @test */
    public function optionsRequestForCurrencyCollection(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->fetchOptionsForCurrencyCollection();
        $response->assertStatus(200);
    }

    /** @test */
    public function optionsRequestForCurrency(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->getToCurrencyList(['offset' => 0, 'limit' => 1]);
        $currency_id = $response->json()[0]['id'];

        $response = $this->fetchOptionsForCurrency(['currency_id' => $currency_id]);
        $response->assertStatus(200);
    }
}
