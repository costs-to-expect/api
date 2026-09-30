<?php

namespace Tests\Action\Http;

use App\User;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class RateLimiterTest extends TestCase
{
    private const AUTHENTICATED_LIMIT = 300;
    private const UNAUTHENTICATED_LIMIT = 120;

    protected function setUp(): void
    {
        parent::setUp();

        // TestCase::setUp() replaces ThrottleRequests with a pass-through, put the real limiter back
        $this->withMiddleware(ThrottleRequests::class);
    }

    #[Test]
    public function authenticatedRequestUsesPerUserLimit(): void
    {
        $token = $this->tokenFor($this->createUser());

        $first = $this->getCurrencies($token);
        $second = $this->getCurrencies($token);

        $this->assertRateLimit($first, self::AUTHENTICATED_LIMIT, self::AUTHENTICATED_LIMIT - 1);
        $this->assertRateLimit($second, self::AUTHENTICATED_LIMIT, self::AUTHENTICATED_LIMIT - 2);
    }

    #[Test]
    public function unauthenticatedRequestUsesIpLimit(): void
    {
        $first = $this->getCurrencies();
        $second = $this->getCurrencies();

        $this->assertRateLimit($first, self::UNAUTHENTICATED_LIMIT, self::UNAUTHENTICATED_LIMIT - 1);
        $this->assertRateLimit($second, self::UNAUTHENTICATED_LIMIT, self::UNAUTHENTICATED_LIMIT - 2);
    }

    #[Test]
    public function invalidBearerIsTreatedAsUnauthenticated(): void
    {
        $first = $this->getCurrencies('999|not-a-real-token');
        $second = $this->getCurrencies();

        $this->assertRateLimit($first, self::UNAUTHENTICATED_LIMIT, self::UNAUTHENTICATED_LIMIT - 1);
        $this->assertRateLimit($second, self::UNAUTHENTICATED_LIMIT, self::UNAUTHENTICATED_LIMIT - 2);
    }

    #[Test]
    public function differentUsersFromTheSameIpHaveSeparateBuckets(): void
    {
        $token_one = $this->tokenFor($this->createUser());
        $token_two = $this->tokenFor($this->createUser());

        for ($i = 0; $i < self::AUTHENTICATED_LIMIT; $i++) {
            $this->getCurrencies($token_one)->assertOk();
        }

        $this->getCurrencies($token_one)->assertStatus(429);

        $response = $this->getCurrencies($token_two);

        $response->assertOk();
        $this->assertRateLimit($response, self::AUTHENTICATED_LIMIT, self::AUTHENTICATED_LIMIT - 1);
    }

    #[Test]
    public function authenticatedRequestsDoNotConsumeTheIpBucket(): void
    {
        $token = $this->tokenFor($this->createUser());

        for ($i = 0; $i < 10; $i++) {
            $this->getCurrencies($token)->assertOk();
        }

        $response = $this->getCurrencies();

        $this->assertRateLimit($response, self::UNAUTHENTICATED_LIMIT, self::UNAUTHENTICATED_LIMIT - 1);
    }

    #[Test]
    public function authenticatedWriteAndReadShareTheSameBucket(): void
    {
        $token = $this->tokenFor($this->createUser());

        $write = $this->postJson(
            route('resource-type.create'),
            [
                'name' => $this->faker->text(200),
                'description' => $this->faker->text(200),
                'data' => '{"field":true}',
                'item_type_id' => $this->item_types['allocated-expense'],
                'public' => false
            ],
            $this->bearerHeader($token)
        );

        $write->assertStatus(201);
        $this->assertRateLimit($write, self::AUTHENTICATED_LIMIT, self::AUTHENTICATED_LIMIT - 1);

        $read = $this->getCurrencies($token);

        $read->assertOk();
        $this->assertRateLimit($read, self::AUTHENTICATED_LIMIT, self::AUTHENTICATED_LIMIT - 2);
    }

    /**
     * Real requests boot a fresh application, the test client keeps one, so the `api` guard would
     * otherwise hand every request after the first the first request's user.
     */
    private function getCurrencies(?string $token = null): TestResponse
    {
        Auth::forgetGuards();

        return $this->getJson(route('currency.list'), $this->bearerHeader($token));
    }

    private function bearerHeader(?string $token): array
    {
        return $token === null ? [] : ['Authorization' => 'Bearer ' . $token];
    }

    private function tokenFor(User $user): string
    {
        return $user->createToken('rate-limiter-test')->plainTextToken;
    }

    private function assertRateLimit(TestResponse $response, int $limit, int $remaining): void
    {
        $this->assertSame((string) $limit, $response->headers->get('X-RateLimit-Limit'));
        $this->assertSame((string) $remaining, $response->headers->get('X-RateLimit-Remaining'));
    }
}
