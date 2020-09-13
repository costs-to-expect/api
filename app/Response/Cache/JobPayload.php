<?php
declare(strict_types=1);

namespace App\Response\Cache;

/**
 * Generate the data we need the ClearResourceTypeIdCache job. The job
 * is responsible for fetching and clearing the keys, we pass in the minimum
 * necessary
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class JobPayload
{
    private array $payload;

    public function __construct(
        int $cache_prefix = null,
        bool $permitted_user = false
    )
    {
        $this->payload = [
            'cache_prefix' => $cache_prefix,
            'permitted_user' => $permitted_user,
            'public_resource_types' => [],
            'route_parameters' => [],
            'permitted_users' => [],
            'key' => ''
        ];
    }

    public function setPublicResourceTypes(array $resource_types): JobPayload
    {
        $this->payload['public_resource_types'] = $resource_types;

        return $this;
    }

    public function setRouteParameters(array $parameters): JobPayload
    {
        $this->payload['route_parameters'] = $parameters;

        return $this;
    }

    public function setPermittedUsers(array $users): JobPayload
    {
        $this->payload['permitted_users'] = $users;

        return $this;
    }

    public function groupKey(string $key): JobPayload
    {
        $this->payload['key'] = $key;

        return $this;
    }

    public function payload(): array
    {
        return $this->payload;
    }
}
