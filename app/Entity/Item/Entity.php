<?php

namespace App\Entity\Item;

use App\Entity\Config;
use App\Models\ResourceTypeItemType;

class Entity
{
    public static function item(int $resource_type_id): Config
    {
        $type =(new ResourceTypeItemType())->itemType($resource_type_id);

        if ($type !== null) {
            switch ($type) {
                case 'allocated-expense':
                    return new AllocatedExpense();

                case 'simple-expense':
                    return new SimpleExpense();

                case 'simple-item':
                    return new SimpleItem();

                default:
                    throw new \OutOfRangeException('No entity definition for ' . $type, 500);
                    break;
            }
        } else {
            throw new \RuntimeException('No entity definition for ' . $type, 500);
        }
    }
}
