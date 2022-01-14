<?php

namespace App\ItemType;

use App\ItemType\AllocatedExpense\Item as AllocatedExpenseItem;
use App\ItemType\Game\Item as GameItem;
use App\ItemType\SimpleExpense\Item as SimpleExpenseItem;
use App\ItemType\SimpleItem\Item as SimpleItemItem;
use App\Models\ResourceTypeItemType;

class Entity
{
    public static function item(int $resource_type_id): ItemType
    {
        $type = (new ResourceTypeItemType())->itemType($resource_type_id);

        if ($type !== null) {
            return self::byType($type);
        }

        throw new \RuntimeException('No entity definition for ' . $type, 500);
    }

    public static function byType(string $item_type): ItemType
    {
        return match ($item_type) {
            'allocated-expense' => new AllocatedExpenseItem(),
            'simple-expense' => new SimpleExpenseItem(),
            'simple-item' => new SimpleItemItem(),
            'game' => new GameItem(),
            default => throw new \OutOfRangeException('No entity definition for ' . $item_type, 500),
        };
    }
}
