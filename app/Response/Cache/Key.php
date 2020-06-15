<?php
declare(strict_types=1);

namespace App\Response\Cache;

use App\Utilities\Hash;

class Key
{
    private Hash $hash;

    public function __construct()
    {
        $this->hash = new Hash();
    }

    /**
     * @param int $resource_type_id
     *
     * @return string
     */
    public function partialTransfers(int $resource_type_id): string
    {
        return '/v2/resource-types/' .
            $this->hash->encode('resource_type', $resource_type_id) .
            '/partial-transfers';
    }

    /**
     * @param int $resource_type_id
     *
     * @return string
     */
    public function resources(int $resource_type_id): string
    {
        return '/v2/resource-types/' .
            $this->hash->encode('resource_type', $resource_type_id) .
            '/resources';
    }

    /**
     * @return string
     */
    public function resourcesTypes(): string
    {
        return '/v2/resource-types';
    }

    /**
     * @param int $resource_type_id
     *
     * @return string
     */
    public function transfers(int $resource_type_id): string
    {
        return '/v2/resource-types/' .
            $this->hash->encode('resource_type', $resource_type_id) .
            '/transfers';
    }
}
