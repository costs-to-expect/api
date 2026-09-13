<?php

namespace Tests\View\Http\Controllers;

use Tests\TestCase;

final class QueueTest extends TestCase
{
    /** @test */
    public function queueCollection(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->getToQueueList();
        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 0);
        $this->assertEquals([], $response->json());
    }

    /** @test */
    public function queueShowInvalidId(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->getToQueueShow(['queue_id' => 'xxxxxxxxxx']);
        $response->assertStatus(403);
    }

    /** @test */
    public function optionsRequestForQueueCollection(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->fetchOptionsForQueueCollection();
        $response->assertStatus(200);
    }

    /** @test */
    public function optionsRequestForQueueInvalidId(): void
    {
        $this->actingAs($this->createUser());

        $response = $this->fetchOptionsForQueue(['queue_id' => 'xxxxxxxxxx']);
        $response->assertStatus(403);
    }
}
