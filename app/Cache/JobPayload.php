<?php
declare(strict_types=1);

namespace App\Cache;

/**
 * Generate the data we need the ClearResourceTypeIdCache job. The job
 * is responsible for fetching and clearing the keys, we pass in the minimum
 * necessary
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2022
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class JobPayload
{
    private array $payload;

    public function __construct()
    {
        $this->payload = [
            'is_permitted_user' => false,
            'route_parameters' => [],
            'key' => null,
            'user_id' => null
        ];
    }

    public function setUserId(int $id): JobPayload
    {
        $this->payload['user_id'] = $id;

        return $this;
    }

    public function isPermittedUser(bool $permitted = false): JobPayload
    {
        $this->payload['is_permitted_user'] = $permitted;

        return $this;
    }

    public function setRouteParameters(array $parameters): JobPayload
    {
        $this->payload['route_parameters'] = $parameters;

        return $this;
    }

    public function setGroupKey(string $key): JobPayload
    {
        $this->payload['group_key'] = $key;

        return $this;
    }

    public function payload(): array
    {
        return $this->payload;
    }
}
