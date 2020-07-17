<?php

declare(strict_types=1);

namespace App\Response\Cache;

class Trash
{
    private Control $cache_control;

    private array $keys;

    private int $resource_type_id;

    private array $public_resource_type_ids;

    private array $permitted_users;

    public function __construct(
        Control $cache_control,
        array $keys,
        int $resource_type_id,
        array $public_resource_type_ids,
        array $permitted_users
    )
    {
        $this->cache_control = $cache_control;
        $this->keys = $keys;
        $this->resource_type_id = $resource_type_id;
        $this->public_resource_type_ids = $public_resource_type_ids;
        $this->permitted_users = $permitted_users;
    }

    public function all(): void
    {
        $this->cache_control->clearPrivateCacheKeys($this->keys);

        foreach ($this->permitted_users as $permitted_user) {
            $cache_control_for_permitted_user = new Control($permitted_user);
            $cache_control_for_permitted_user->clearPrivateCacheKeys($this->keys);
        }

        if (in_array($this->resource_type_id, $this->public_resource_type_ids, true)) {
            $this->cache_control->clearPublicCacheKeys($this->keys);
        }
    }
}
