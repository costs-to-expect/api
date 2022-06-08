<?php
declare(strict_types=1);

namespace App\Cache;

/**
 * Decode the payload for a job
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
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

    public function isPermittedUser(): bool
    {
        return $this->payload['is_permitted_user'];
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
