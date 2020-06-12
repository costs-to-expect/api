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
     * Return the URI for the resources collection
     *
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
}
