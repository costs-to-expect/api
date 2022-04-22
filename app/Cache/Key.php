<?php
declare(strict_types=1);

namespace App\Cache;

use App\HttpRequest\Hash;

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
    public function categories(int $resource_type_id): string
    {
        return '/v2/resource-types/' .
            $this->hash->encode('resource-type', $resource_type_id) .
            '/categories';
    }

    /**
     * @param int $resource_type_id
     * @param int $resource_id
     *
     * @return string
     */
    public function items(int $resource_type_id, int $resource_id): string
    {
        return '/v2/resource-types/' .
            $this->hash->encode('resource-type', $resource_type_id) .
            '/resources/' . $this->hash->encode('resource', $resource_id) .
            '/items';
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

    public function permittedResourceTypes(): string
    {
        return '/v2/permitted-resource-types';
    }

    public function viewableResourceTypes(): string
    {
        return '/v2/viewable-resource-types';
    }

    /**
     * @param int $resource_type_id
     *
     * @return string
     */
    public function permittedUsers(int $resource_type_id): string
    {
        return '/v2/resource-types/' .
            $this->hash->encode('resource-type', $resource_type_id) .
            '/permitted-users';
    }

    /**
     * @param int $resource_type_id
     *
     * @return string
     */
    public function resources(int $resource_type_id): string
    {
        return '/v2/resource-types/' .
            $this->hash->encode('resource-type', $resource_type_id) .
            '/resources';
    }

    /**
     * @param int $resource_type_id
     *
     * @return string
     */
    public function resourceTypeItems(int $resource_type_id): string
    {
        return '/v2/resource-types/' .
            $this->hash->encode('resource-type', $resource_type_id) .
            '/items';
    }

    /**
     * @param int $resource_type_id
     *
     * @return string
     */
    public function resourceType(int $resource_type_id): string
    {
        return '/v2/resource-types/' .
            $this->hash->encode('resource-type', $resource_type_id);
    }

    /**
     * @return string
     */
    public function resourceTypes(): string
    {
        return '/v2/resource-types';
    }

    /**
     * @param int $resource_type_id
     * @param int $category_id
     *
     * @return string
     */
    public function subcategories(int $resource_type_id, int $category_id): string
    {
        return '/v2/resource-types/' .
            $this->hash->encode('resource-type', $resource_type_id) .
            '/categories/' . $this->hash->encode('category', $category_id) .
            '/subcategories';
    }

    /**
     * @param int $resource_type_id
     *
     * @return string
     */
    public function transfers(int $resource_type_id): string
    {
        return '/v2/resource-types/' .
            $this->hash->encode('resource-type', $resource_type_id) .
            '/transfers';
    }
}
