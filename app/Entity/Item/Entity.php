<?php

namespace App\Entity\Item;

use App\Models\ResourceTypeItemType;

class Entity
{
    public static function item(int $resource_type_id): Item
    {
        $type =(new ResourceTypeItemType())->itemType($resource_type_id);

        if ($type !== null) {
            return self::byType($type);
        }

        throw new \RuntimeException('No entity definition for ' . $type, 500);
    }

    public static function byType(string $item_type): Item
    {
        switch ($item_type) {
            case 'allocated-expense':
                return new AllocatedExpense();

            case 'simple-expense':
                return new SimpleExpense();

            case 'simple-item':
                return new SimpleItem();

            default:
                throw new \OutOfRangeException('No entity definition for ' . $item_type, 500);
        }
    }
}
