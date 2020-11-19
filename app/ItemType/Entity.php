<?php

namespace App\ItemType;

use App\Entity\Item\AllocatedExpense;
use App\Entity\Item\Game;
use App\Entity\Item\SimpleExpense;
use App\Entity\Item\SimpleItem;
use App\Models\ResourceTypeItemType;

class Entity
{
    public static function item(int $resource_type_id): ItemType
    {
        $type =(new ResourceTypeItemType())->itemType($resource_type_id);

        if ($type !== null) {
            return self::byType($type);
        }

        throw new \RuntimeException('No entity definition for ' . $type, 500);
    }

    public static function byType(string $item_type): ItemType
    {
        switch ($item_type) {
            case 'allocated-expense':
                return new AllocatedExpense();

            case 'simple-expense':
                return new SimpleExpense();

            case 'simple-item':
                return new SimpleItem();

            case 'game':
                return new Game();

            default:
                throw new \OutOfRangeException('No entity definition for ' . $item_type, 500);
        }
    }
}
