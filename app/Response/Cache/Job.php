<?php
declare(strict_types=1);

namespace App\Response\Cache;

/**
 * Decode the payload for a job
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2021
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Job
{
    private array $payload;

    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    public function userId(): ?int
    {
        return $this->payload['user_id'];
    }

    public function permittedUser(): bool
    {
        return $this->payload['permitted_user'];
    }

    public function groupKey(): string
    {
        return $this->payload['group_key'];
    }

    public function routeParameters(): array
    {
        return $this->payload['route_parameters'];
    }
}
