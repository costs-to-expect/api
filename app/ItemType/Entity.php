<?php

namespace App\ItemType;

use App\Models\ResourceTypeItemType;

class Entity
{
    public static function itemType(int $resource_type_id): string
    {
        $type = (new ResourceTypeItemType())->itemType($resource_type_id);

        if ($type !== null) {
            return $type;
        }

        throw new \RuntimeException('No entity definition for ' . $type, 500);
    }
}
