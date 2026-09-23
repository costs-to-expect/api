<?php

namespace Tests\Jobs;

use App\Cache\JobPayload;
use App\Cache\KeyGroup;
use App\HttpRequest\Hash;
use App\Jobs\ClearCache;
use App\Notifications\FailedJob;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ClearCacheTest extends TestCase
{
    /** @test */
    public function handleClearsUserScopedKeysWithNoResourceTypeInPayload(): void
    {
        Notification::fake();

        $user = $this->createUser();
        $this->actingAs($user);

        $payload = (new JobPayload())
            ->setGroupKey(KeyGroup::RESOURCE_TYPE_CREATE)
            ->setRouteParameters([])
            ->setUserId($user->id)
            ->payload();

        $job = new ClearCache($payload);
        $job->handle();

        Notification::assertSentOnDemandTimes(FailedJob::class, 0);
    }

    /** @test */
    public function handleClearsKeysForAdditionalPermittedUsersOnAPrivateResourceType(): void
    {
        Notification::fake();

        $owner = $this->createUser();
        $this->actingAs($owner);

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType(['public' => false]);

        $other_user = $this->createUser();
        $this->postToPermittedUserCreate($resource_type_id, ['email' => $other_user->email])
            ->assertStatus(204);

        $raw_resource_type_id = (new Hash())->decode('resource-type', $resource_type_id);

        $payload = (new JobPayload())
            ->setGroupKey(KeyGroup::RESOURCE_TYPE_UPDATE)
            ->setRouteParameters(['resource_type_id' => $raw_resource_type_id])
            ->setUserId($owner->id)
            ->payload();

        $job = new ClearCache($payload);
        $job->handle();

        Notification::assertSentOnDemandTimes(FailedJob::class, 0);
    }

    /** @test */
    public function handleClearsPublicKeysForAPublicResourceType(): void
    {
        Notification::fake();

        $owner = $this->createUser();
        $this->actingAs($owner);

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType(['public' => true]);

        $raw_resource_type_id = (new Hash())->decode('resource-type', $resource_type_id);

        $payload = (new JobPayload())
            ->setGroupKey(KeyGroup::RESOURCE_TYPE_UPDATE)
            ->setRouteParameters(['resource_type_id' => $raw_resource_type_id])
            ->setUserId($owner->id)
            ->payload();

        $job = new ClearCache($payload);
        $job->handle();

        Notification::assertSentOnDemandTimes(FailedJob::class, 0);
    }

    /** @test */
    public function failedSendsAFailedJobNotification(): void
    {
        Notification::fake();

        $job = new ClearCache(['user_id' => 999999, 'route_parameters' => [], 'group_key' => 'test']);
        $job->failed(new \Exception('Cache clear blew up'));

        Notification::assertSentOnDemand(
            FailedJob::class,
            function (FailedJob $notification) {
                $mail = $notification->toMail(null);

                return str_contains(implode(' ', $mail->introLines), 'Cache clear blew up');
            }
        );
    }
}
