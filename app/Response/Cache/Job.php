<?php
declare(strict_types=1);

namespace App\Response\Cache;

/**
 * Decode the payload for a job
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Job
{
    private array $payload;

    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    public function cachePrefix(): ?int
    {
        return $this->payload['cache_prefix'];
    }

    public function permittedUser(): bool
    {
        return $this->payload['permitted_user'];
    }

    public function groupKey(): string
    {
        return $this->payload['key'];
    }

    public function permittedUsers(): array
    {
        return $this->payload['permitted_users'];
    }

    public function parameters(): array
    {
        return $this->payload['route_parameters'];
    }

    public function publicResourceTypes(): array
    {
        return $this->payload['public_resource_types'];
    }
}
